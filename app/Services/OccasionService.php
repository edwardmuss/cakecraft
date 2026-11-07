<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\CustomerOccasion;
use App\Models\Invoice;
use App\Models\OccasionReminder;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection as Coll;

/**
 * Remembers yearly occasions (birthday, anniversary...) from past cake orders
 * and prepares "are you planning another one this year?" reminders.
 */
class OccasionService
{
    public const DEFAULT_TEMPLATE = "Hi {customer}, last year you ordered a {occasion} cake for {recipient} ({last_cake}). "
        . "{recipient}'s {occasion} is coming up on {date}. Are you planning to have a cake this year too? "
        . "Reply to book. - {business}";

    public const TYPES = [
        'birthday'    => 'Birthday',
        'anniversary' => 'Anniversary',
        'wedding'     => 'Wedding',
        'graduation'  => 'Graduation',
        'baby_shower' => 'Baby shower',
        'other'       => 'Other',
    ];

    /** Occasion types that repeat every year. Wedding/baby shower/graduation are one-off by default. */
    public const RECURRING = ['birthday', 'anniversary'];

    public static function setting(int $branchId, string $key, $default = null)
    {
        return Setting::where('branch_id', $branchId)->where('key', $key)->value('value') ?? $default;
    }

    public static function saveSetting(int $branchId, string $key, $value): void
    {
        Setting::updateOrCreate(['branch_id' => $branchId, 'key' => $key], ['value' => $value]);
    }

    /** Create/update the remembered occasion from an invoice that has occasion details. */
    public function syncFromInvoice(Invoice $invoice): ?CustomerOccasion
    {
        if (!$invoice->occasion_type || !$invoice->occasion_date) {
            return null;
        }

        $recurring = in_array($invoice->occasion_type, self::RECURRING, true)
            || Collection::where('branch_id', $invoice->branch_id)
                ->where('occasion_type', $invoice->occasion_type)->where('is_recurring', true)->exists();
        if (!$recurring) {
            return null;
        }

        $date = Carbon::parse($invoice->occasion_date);
        $cake = $invoice->items()->get()->map(fn ($i) => trim($i->item_name . ($i->size_label ? ', ' . $i->size_label : '')))->implode(' + ');

        // Same celebrant => same occasion, even if the date shifts a day or two between years.
        $match = [
            'branch_id' => $invoice->branch_id,
            'customer_id' => $invoice->customer_id,
            'occasion_type' => $invoice->occasion_type,
        ];
        if ($invoice->recipient_name) {
            $match['recipient_name'] = $invoice->recipient_name;
        } else {
            $match['recipient_name'] = null;
            $match['month'] = $date->month;
            $match['day'] = $date->day;
        }
        $occasion = CustomerOccasion::firstOrNew($match);
        $occasion->month = $date->month;
        $occasion->day = $date->day;

        // Only move "last" forward, never backwards
        if (!$occasion->exists || !$occasion->last_occasion_date || $date->gte($occasion->last_occasion_date)) {
            $occasion->last_occasion_date = $date;
            $occasion->last_invoice_id = $invoice->id;
            $occasion->last_cake = $cake;
        }
        $occasion->save();

        return $occasion;
    }

    /**
     * Occasions falling within the next $days days (inclusive), with this year's
     * reminder status. Only occasions whose last cake was before the upcoming date.
     */
    public function upcoming(int $branchId, int $days, bool $includeSent = true): Coll
    {
        $today = Carbon::today();
        $end = $today->copy()->addDays($days);

        return CustomerOccasion::with(['customer', 'lastInvoice'])
            ->where('branch_id', $branchId)
            ->where('reminders_enabled', true)
            ->get()
            ->map(function (CustomerOccasion $o) use ($today) {
                $o->next_date = $o->nextDate($today);
                $o->days_away = (int) $today->diffInDays($o->next_date, false);
                return $o;
            })
            // not already ordered for this coming date
            ->filter(fn ($o) => !$o->last_occasion_date || $o->last_occasion_date->lt($o->next_date->copy()->startOfYear()))
            ->filter(fn ($o) => $o->next_date->lte($end))
            ->each(function ($o) {
                $o->reminded = $o->reminderSentFor($o->next_date->year);
            })
            ->when(!$includeSent, fn ($c) => $c->reject(fn ($o) => $o->reminded))
            ->sortBy('days_away')
            ->values();
    }

    public function buildMessage(CustomerOccasion $o, ?string $template = null): string
    {
        $template = $template ?: self::setting($o->branch_id, 'occasion_reminder_template', self::DEFAULT_TEMPLATE);
        $next = $o->next_date ?? $o->nextDate();
        $typeLabel = strtolower(self::TYPES[$o->occasion_type] ?? $o->occasion_type);

        return strtr($template, [
            '{customer}'  => explode(' ', trim($o->customer->name))[0] ?? $o->customer->name,
            '{recipient}' => $o->recipient_name ?: 'your loved one',
            '{occasion}'  => $typeLabel,
            '{last_cake}' => $o->last_cake ?: 'a cake',
            '{date}'      => $next->format('j F'),
            '{last_year}' => (string) ($o->last_occasion_date?->year ?? $next->year - 1),
            '{business}'  => self::setting($o->branch_id, 'business_name', \App\Support\Brand::name()),
        ]);
    }

    public function send(CustomerOccasion $o, ?string $message = null, ?int $userId = null): OccasionReminder
    {
        $next = $o->next_date ?? $o->nextDate();
        $message = $message ?: $this->buildMessage($o);
        $status = 'sent';

        try {
            $resp = app(SMSService::class)->sendSMS($o->customer->phone, $message);
            if (is_array($resp) && (($resp['success'] ?? true) === false || isset($resp['error']))) {
                $status = 'failed';
            }
        } catch (\Throwable $e) {
            report($e);
            $status = 'failed';
        }

        return $o->reminders()->create([
            'year' => $next->year,
            'message' => $message,
            'status' => $status,
            'sent_by' => $userId,
            'sent_at' => now(),
        ]);
    }
}

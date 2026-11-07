<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerOccasion;
use App\Models\OccasionReminder;
use App\Services\OccasionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OccasionReminderController extends Controller
{
    public function __construct(private OccasionService $service) {}

    public function index(Request $request)
    {
        abort_if(!Auth::user()->can('view customers'), 403);
        $branchId = Auth::user()->branch_id;

        $leadDays = (int) ($request->days ?? OccasionService::setting($branchId, 'occasion_lead_days', 14));
        $leadDays = max(1, min($leadDays, 365));

        $upcoming = $this->service->upcoming($branchId, $leadDays);
        if ($request->filled('type')) {
            $upcoming = $upcoming->where('occasion_type', $request->type)->values();
        }
        foreach ($upcoming as $o) {
            $o->preview = $this->service->buildMessage($o);
        }

        $stats = [
            'upcoming' => $upcoming->count(),
            'pending' => $upcoming->where('reminded', false)->count(),
            'sent' => $upcoming->where('reminded', true)->count(),
            'tracked' => CustomerOccasion::where('branch_id', $branchId)->count(),
        ];

        $history = OccasionReminder::with('occasion.customer')
            ->whereHas('occasion', fn ($q) => $q->where('branch_id', $branchId))
            ->latest()->limit(25)->get();

        $template = OccasionService::setting($branchId, 'occasion_reminder_template', OccasionService::DEFAULT_TEMPLATE);
        $autoSend = OccasionService::setting($branchId, 'occasion_auto_send', '0') === '1';
        $types = OccasionService::TYPES;
        $customers = Customer::where('branch_id', $branchId)->orderBy('name')->get(['id', 'name', 'phone']);

        return view('occasions.index', compact('upcoming', 'stats', 'history', 'template', 'autoSend', 'leadDays', 'types', 'customers', 'request'));
    }

    public function saveSettings(Request $request)
    {
        abort_if(!Auth::user()->can('update customers'), 403);
        $data = $request->validate([
            'template' => 'required|string|max:600',
            'lead_days' => 'required|integer|min:1|max:120',
        ]);
        $b = Auth::user()->branch_id;
        OccasionService::saveSetting($b, 'occasion_reminder_template', $data['template']);
        OccasionService::saveSetting($b, 'occasion_lead_days', $data['lead_days']);
        OccasionService::saveSetting($b, 'occasion_auto_send', $request->boolean('auto_send') ? '1' : '0');
        return redirect()->route('occasions.index')->with('success', 'Reminder settings saved.');
    }

    public function send(Request $request, $id)
    {
        abort_if(!Auth::user()->can('update customers'), 403);
        $o = CustomerOccasion::with('customer')->where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $o->next_date = $o->nextDate();
        $reminder = $this->service->send($o, $request->input('message'), Auth::id());

        return back()->with($reminder->status === 'sent' ? 'success' : 'error',
            $reminder->status === 'sent' ? "Reminder sent to {$o->customer->name}." : 'The SMS could not be sent. Check SMS settings.');
    }

    public function sendBulk(Request $request)
    {
        abort_if(!Auth::user()->can('update customers'), 403);
        $ids = (array) $request->input('ids', []);
        $sent = 0; $failed = 0;

        $occasions = CustomerOccasion::with('customer')->where('branch_id', Auth::user()->branch_id)->whereIn('id', $ids)->get();
        foreach ($occasions as $o) {
            $o->next_date = $o->nextDate();
            if ($o->reminderSentFor($o->next_date->year)) continue;
            $this->service->send($o, null, Auth::id())->status === 'sent' ? $sent++ : $failed++;
        }

        return back()->with($failed ? 'error' : 'success', "$sent reminder(s) sent" . ($failed ? ", $failed failed." : '.'));
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->can('update customers'), 403);
        $data = $request->validate([
            'customer_id' => 'required|integer',
            'occasion_type' => 'required|string|max:40',
            'recipient_name' => 'nullable|string|max:120',
            'date' => 'required|date',
            'last_cake' => 'nullable|string|max:200',
        ]);
        $customer = Customer::where('branch_id', Auth::user()->branch_id)->findOrFail($data['customer_id']);
        $date = \Carbon\Carbon::parse($data['date']);

        CustomerOccasion::updateOrCreate([
            'branch_id' => $customer->branch_id, 'customer_id' => $customer->id,
            'occasion_type' => $data['occasion_type'], 'recipient_name' => $data['recipient_name'] ?? null,
            'month' => $date->month, 'day' => $date->day,
        ], ['last_occasion_date' => $date, 'last_cake' => $data['last_cake'] ?? null]);

        return back()->with('success', 'Occasion saved.');
    }

    public function toggle($id)
    {
        abort_if(!Auth::user()->can('update customers'), 403);
        $o = CustomerOccasion::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $o->update(['reminders_enabled' => !$o->reminders_enabled]);
        return back()->with('success', 'Updated.');
    }
}

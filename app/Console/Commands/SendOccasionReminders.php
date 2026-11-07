<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Services\OccasionService;
use Illuminate\Console\Command;

class SendOccasionReminders extends Command
{
    protected $signature = 'occasions:remind {--dry-run}';
    protected $description = 'Auto-send occasion reminders (only for branches with auto-send enabled)';

    public function handle(OccasionService $service): int
    {
        foreach (Branch::all() as $branch) {
            if (OccasionService::setting($branch->id, 'occasion_auto_send', '0') !== '1') {
                continue;
            }
            $lead = (int) OccasionService::setting($branch->id, 'occasion_lead_days', 14);
            // Only those exactly at the lead window start, so each is sent once
            $due = $service->upcoming($branch->id, $lead, false)->filter(fn ($o) => $o->days_away <= $lead);

            foreach ($due as $o) {
                if ($this->option('dry-run')) {
                    $this->line("Would remind {$o->customer->name} ({$o->occasion_type}, {$o->next_date->toDateString()})");
                    continue;
                }
                $service->send($o);
            }
            $this->info("{$branch->id}: {$due->count()} reminder(s) processed.");
        }
        return self::SUCCESS;
    }
}

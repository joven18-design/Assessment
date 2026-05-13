<?php

namespace App\Console\Commands;

use App\Services\EscalationService;
use Illuminate\Console\Command;

class CheckEscalation extends Command
{
    protected $signature = 'issues:check-escalation';

    protected $description = 'Check all open issues for time-based escalation rules';

    public function handle(EscalationService $escalationService): int
    {
        $this->info('Checking issues for escalation...');

        $escalatedIds = $escalationService->checkAllForEscalation();

        if (empty($escalatedIds)) {
            $this->info('No issues needed escalation.');
        } else {
            $this->info('Escalated ' . count($escalatedIds) . ' issue(s): #' . implode(', #', $escalatedIds));
        }

        return Command::SUCCESS;
    }
}

<?php

namespace App\Services;

use App\Models\Issue;

class EscalationService
{
    /**
     * Check and apply escalation rules to an issue.
     * Returns true if the issue was escalated.
     */
    public function evaluate(Issue $issue): bool
    {
        $shouldEscalate = false;

        // Rule 1: Critical priority issues are always escalated
        if ($issue->priority === 'critical') {
            $shouldEscalate = true;
        }

        // Rule 2: Security category issues are always escalated
        if ($issue->category === 'security') {
            $shouldEscalate = true;
        }

        // Rule 3: High priority + open for more than 24 hours
        if ($issue->priority === 'high' && $issue->status === 'open') {
            if ($issue->created_at && $issue->created_at->diffInHours(now()) >= 24) {
                $shouldEscalate = true;
            }
        }

        if ($shouldEscalate && !$issue->is_escalated) {
            $issue->is_escalated = true;
            $issue->save();
        }

        return $shouldEscalate;
    }

    /**
     * Check all open issues for time-based escalation.
     * Used by the scheduled Artisan command.
     */
    public function checkAllForEscalation(): array
    {
        $escalated = [];

        // Find high-priority open issues older than 24 hours that aren't already escalated
        $issues = Issue::where('priority', 'high')
            ->where('status', 'open')
            ->where('is_escalated', false)
            ->where('created_at', '<=', now()->subHours(24))
            ->get();

        foreach ($issues as $issue) {
            $this->evaluate($issue);
            $escalated[] = $issue->id;
        }

        return $escalated;
    }
}

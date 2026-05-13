<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Services\EscalationService;
use App\Services\IssueSummaryService;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function __construct(
        private IssueSummaryService $summaryService,
        private EscalationService $escalationService
    ) {}

    public function index(Request $request)
    {
        $query = Issue::query();

        if ($request->filled('status')) {
            $query->byStatus($request->input('status'));
        }
        if ($request->filled('category')) {
            $query->byCategory($request->input('category'));
        }
        if ($request->filled('priority')) {
            $query->byPriority($request->input('priority'));
        }

        $issues = $query->orderBy('created_at', 'desc')->get();

        return view('issues.index', compact('issues'));
    }

    public function create()
    {
        return view('issues.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'priority' => 'required|string|in:' . implode(',', Issue::PRIORITIES),
            'category' => 'required|string|in:' . implode(',', Issue::CATEGORIES),
        ]);

        $issue = Issue::create($validated);

        // Generate AI summary
        $summaryResult = $this->summaryService->generate(
            $issue->title,
            $issue->description,
            $issue->priority,
            $issue->category
        );
        $issue->update([
            'summary' => $summaryResult['summary'],
            'suggested_action' => $summaryResult['suggested_action'],
        ]);

        // Check escalation
        $this->escalationService->evaluate($issue);

        return redirect()->route('issues.show', $issue)->with('success', 'Issue created successfully.');
    }

    public function show(Issue $issue)
    {
        return view('issues.show', compact('issue'));
    }

    public function edit(Issue $issue)
    {
        return view('issues.edit', compact('issue'));
    }

    public function update(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|min:10',
            'priority' => 'sometimes|string|in:' . implode(',', Issue::PRIORITIES),
            'category' => 'sometimes|string|in:' . implode(',', Issue::CATEGORIES),
            'status' => 'sometimes|string|in:' . implode(',', Issue::STATUSES),
        ]);

        $issue->update($validated);

        // Re-generate summary if content changed
        if ($request->has('description') || $request->has('title')) {
            $summaryResult = $this->summaryService->generate(
                $issue->title,
                $issue->description,
                $issue->priority,
                $issue->category
            );
            $issue->update([
                'summary' => $summaryResult['summary'],
                'suggested_action' => $summaryResult['suggested_action'],
            ]);
        }

        // Re-check escalation
        $this->escalationService->evaluate($issue);

        return redirect()->route('issues.show', $issue)->with('success', 'Issue updated successfully.');
    }
}

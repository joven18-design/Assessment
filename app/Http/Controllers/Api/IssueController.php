<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * List all issues with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Issue::query();

        if ($request->has('status')) {
            $query->byStatus($request->input('status'));
        }

        if ($request->has('category')) {
            $query->byCategory($request->input('category'));
        }

        if ($request->has('priority')) {
            $query->byPriority($request->input('priority'));
        }

        if ($request->has('escalated')) {
            $query->escalated();
        }

        $issues = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $issues,
            'count' => $issues->count(),
        ]);
    }

    /**
     * Store a newly created issue.
     */
    public function store(StoreIssueRequest $request): JsonResponse
    {
        $issue = Issue::create($request->validated());

        // Generate AI summary
        $summaryService = app(\App\Services\IssueSummaryService::class);
        $summaryResult = $summaryService->generate(
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
        $escalationService = app(\App\Services\EscalationService::class);
        $escalationService->evaluate($issue);

        return response()->json([
            'success' => true,
            'message' => 'Issue created successfully.',
            'data' => $issue->fresh(),
        ], 201);
    }

    /**
     * Display the specified issue.
     */
    public function show(int $id): JsonResponse
    {
        $issue = Issue::find($id);

        if (!$issue) {
            return response()->json([
                'success' => false,
                'message' => 'Issue not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $issue,
        ]);
    }

    /**
     * Update the specified issue.
     */
    public function update(UpdateIssueRequest $request, int $id): JsonResponse
    {
        $issue = Issue::find($id);

        if (!$issue) {
            return response()->json([
                'success' => false,
                'message' => 'Issue not found.',
            ], 404);
        }

        $issue->update($request->validated());

        // Re-generate summary if description or title changed
        if ($request->has('description') || $request->has('title')) {
            $summaryService = app(\App\Services\IssueSummaryService::class);
            $summaryResult = $summaryService->generate(
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
        $escalationService = app(\App\Services\EscalationService::class);
        $escalationService->evaluate($issue);

        return response()->json([
            'success' => true,
            'message' => 'Issue updated successfully.',
            'data' => $issue->fresh(),
        ]);
    }
}

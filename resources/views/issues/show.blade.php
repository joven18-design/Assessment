@extends('layouts.app')

@section('title', $issue->title)

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('issues.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Back to all issues</a>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $issue->title }}</h1>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-xs px-2 py-0.5 rounded
                        @if($issue->priority === 'critical') bg-red-100 text-red-700
                        @elseif($issue->priority === 'high') bg-orange-100 text-orange-700
                        @elseif($issue->priority === 'medium') bg-yellow-100 text-yellow-700
                        @else bg-gray-100 text-gray-600
                        @endif">
                        {{ ucfirst($issue->priority) }} Priority
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded bg-blue-50 text-blue-700">
                        {{ ucfirst(str_replace('_', ' ', $issue->category)) }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                        {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                    </span>
                    @if($issue->is_escalated)
                        <span class="text-xs px-2 py-0.5 rounded bg-red-100 text-red-800 font-medium">
                            ESCALATED
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('issues.edit', $issue) }}" class="text-sm bg-gray-100 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-200">
                Edit
            </a>
        </div>

        <!-- Description -->
        <div class="mt-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Description</h2>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $issue->description }}</p>
        </div>

        <!-- AI Summary -->
        @if($issue->summary)
        <div class="mt-6 p-4 bg-indigo-50 border border-indigo-100 rounded-md">
            <h2 class="text-sm font-semibold text-indigo-700 uppercase tracking-wide mb-2">AI Summary</h2>
            <p class="text-gray-800">{{ $issue->summary }}</p>
        </div>
        @endif

        <!-- Suggested Action -->
        @if($issue->suggested_action)
        <div class="mt-4 p-4 bg-green-50 border border-green-100 rounded-md">
            <h2 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-2">Suggested Next Action</h2>
            <div class="text-gray-800 space-y-1">
                @if(preg_match('/^\d+\.\s/', $issue->suggested_action))
                    <ol class="list-decimal list-inside space-y-2">
                        @foreach(preg_split('/\n(?=\d+\.)/', $issue->suggested_action) as $step)
                            <li class="pl-1">{{ trim(preg_replace('/^\d+\.\s*/', '', $step)) }}</li>
                        @endforeach
                    </ol>
                @else
                    <p class="whitespace-pre-wrap">{{ $issue->suggested_action }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Metadata -->
        <div class="mt-6 pt-4 border-t text-sm text-gray-400">
            <p>Created: {{ $issue->created_at->format('M d, Y \a\t H:i') }} ({{ $issue->created_at->diffForHumans() }})</p>
            <p>Last updated: {{ $issue->updated_at->format('M d, Y \a\t H:i') }}</p>
            <p>Issue ID: #{{ $issue->id }}</p>
        </div>
    </div>
</div>
@endsection

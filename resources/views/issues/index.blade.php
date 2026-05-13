@extends('layouts.app')

@section('title', 'All Issues')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">All Issues</h1>
    <p class="text-gray-500 mt-1">{{ $issues->count() }} issue(s) found</p>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('issues.index') }}" class="mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            <option value="">All Statuses</option>
            @foreach(\App\Models\Issue::STATUSES as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
        <select name="category" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            <option value="">All Categories</option>
            @foreach(\App\Models\Issue::CATEGORIES as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $cat)) }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Priority</label>
        <select name="priority" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            <option value="">All Priorities</option>
            @foreach(\App\Models\Issue::PRIORITIES as $pri)
                <option value="{{ $pri }}" {{ request('priority') == $pri ? 'selected' : '' }}>
                    {{ ucfirst($pri) }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">
        Filter
    </button>
    <a href="{{ route('issues.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">Clear</a>
</form>

<!-- Issue List -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    @forelse($issues as $issue)
    <a href="{{ route('issues.show', $issue) }}" class="block border-b last:border-b-0 hover:bg-gray-50 transition">
        <div class="p-4 flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h3 class="font-medium text-gray-900">{{ $issue->title }}</h3>
                    @if($issue->is_escalated)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                            ESCALATED
                        </span>
                    @endif
                </div>
                @if($issue->summary)
                    <p class="text-sm text-gray-500 mt-1">{{ $issue->summary }}</p>
                @else
                    <p class="text-sm text-gray-400 mt-1">{{ Str::limit($issue->description, 100) }}</p>
                @endif
                <div class="flex gap-3 mt-2">
                    <span class="text-xs px-2 py-0.5 rounded
                        @if($issue->priority === 'critical') bg-red-100 text-red-700
                        @elseif($issue->priority === 'high') bg-orange-100 text-orange-700
                        @elseif($issue->priority === 'medium') bg-yellow-100 text-yellow-700
                        @else bg-gray-100 text-gray-600
                        @endif">
                        {{ ucfirst($issue->priority) }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded bg-blue-50 text-blue-700">
                        {{ ucfirst(str_replace('_', ' ', $issue->category)) }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                        {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                    </span>
                </div>
            </div>
            <span class="text-xs text-gray-400">{{ $issue->created_at->diffForHumans() }}</span>
        </div>
    </a>
    @empty
    <div class="p-8 text-center text-gray-400">
        No issues found. <a href="{{ route('issues.create') }}" class="text-indigo-600 hover:underline">Create one</a>.
    </div>
    @endforelse
</div>
@endsection

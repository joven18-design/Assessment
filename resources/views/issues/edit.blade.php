@extends('layouts.app')

@section('title', 'Edit: ' . $issue->title)

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('issues.show', $issue) }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Back to issue</a>

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Issue #{{ $issue->id }}</h1>

    <form method="POST" action="{{ route('issues.update', $issue) }}" class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $issue->title) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            @error('title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" id="description" rows="5"
                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('description', $issue->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select name="priority" id="priority"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\Issue::PRIORITIES as $priority)
                        <option value="{{ $priority }}" {{ old('priority', $issue->priority) == $priority ? 'selected' : '' }}>
                            {{ ucfirst($priority) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="category"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\Issue::CATEGORIES as $cat)
                        <option value="{{ $cat }}" {{ old('category', $issue->category) == $cat ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $cat)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\Issue::STATUSES as $status)
                        <option value="{{ $status }}" {{ old('status', $issue->status) == $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                Update Issue
            </button>
            <a href="{{ route('issues.show', $issue) }}" class="text-gray-500 px-4 py-2 text-sm hover:text-gray-700">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

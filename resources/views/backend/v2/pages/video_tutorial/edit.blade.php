@extends('backend.v2.app')

@section('title', 'Update Video Tutorial')

@section('content')
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400">
                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                </span>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Update Video Tutorial</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Update the details for "{{ $data->title }}".
            </p>
        </div>
        <a href="{{ route('admin.video_tutorial.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Tutorials
        </a>
    </div>

    @if ($errors->any())
        <div
            class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-300">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.video_tutorial.update', $data->id) }}">
        @csrf

        <div class="card p-5">
            <div class="space-y-4">
                <div>
                    <label class="form-label" for="title">Title</label>
                    <input type="text" id="title" name="title"
                        class="form-input @error('title') border-red-400 @enderror" placeholder="Enter video title"
                        value="{{ old('title', $data->title) }}">
                    @error('title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label" for="youtube_link">YouTube Link</label>
                    <input type="url" id="youtube_link" name="youtube_link"
                        class="form-input @error('youtube_link') border-red-400 @enderror"
                        placeholder="https://www.youtube.com/watch?v=..."
                        value="{{ old('youtube_link', $data->youtube_link) }}">
                    @error('youtube_link')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" rows="4"
                        class="form-input @error('description') border-red-400 @enderror"
                        placeholder="Short description of the tutorial...">{{ old('description', $data->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('admin.video_tutorial.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Update Tutorial
                </button>
            </div>
        </div>
    </form>
@endsection

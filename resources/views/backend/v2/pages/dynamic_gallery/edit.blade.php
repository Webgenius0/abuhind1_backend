@extends('backend.v2.app')

@section('title', 'Update Dynamic Image')

@section('content')
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400">
                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                </span>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Update Dynamic Image</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Update the details or replace the photos for "{{ $data->title }}".
            </p>
        </div>
        <a href="{{ route('admin.dynamic_gallery.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Images
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

    <form method="POST" action="{{ route('admin.dynamic_gallery.update', $data->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <!-- Details -->
            <div class="card p-5 xl:col-span-2">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Image Set Details</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Describe what users should look for.</p>

                <div class="space-y-4">
                    <div>
                        <label class="form-label" for="type">View Type</label>
                        <select id="type" name="type" class="form-input @error('type') border-red-400 @enderror">
                            <option value="front" @selected(old('type', $data->type) === 'front')>Front View</option>
                            <option value="side" @selected(old('type', $data->type) === 'side')>Side View</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="title">Title</label>
                            <input type="text" id="title" name="title"
                                class="form-input @error('title') border-red-400 @enderror" placeholder="Enter title"
                                value="{{ old('title', $data->title) }}">
                            @error('title')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label" for="subtitle">Subtitle</label>
                            <input type="text" id="subtitle" name="subtitle"
                                class="form-input @error('subtitle') border-red-400 @enderror"
                                placeholder="e.g. FRONT VIEW or SIDE VIEW"
                                value="{{ old('subtitle', $data->subtitle) }}">
                            @error('subtitle')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" rows="4"
                            class="form-input @error('description') border-red-400 @enderror"
                            placeholder="Describe this view...">{{ old('description', $data->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Reference Photos</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Leave a field empty to keep the current image.</p>

                <div class="space-y-5">
                    <div>
                        <label class="form-label" for="right_image">That's Right (Correct)</label>
                        @if ($data->right_image)
                            <img src="{{ $data->right_image }}" alt="That's right"
                                class="w-full h-32 object-cover rounded-xl border border-gray-200 dark:border-gray-700 mb-2">
                        @endif
                        <input type="file" id="right_image" name="right_image" accept="image/*"
                            class="form-input py-2 text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-primary-700 file:text-xs file:font-semibold dark:file:bg-primary-950/50 dark:file:text-primary-400 @error('right_image') border-red-400 @enderror">
                        @error('right_image')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="wrong_image">Not Like That (Incorrect)</label>
                        @if ($data->wrong_image)
                            <img src="{{ $data->wrong_image }}" alt="Not like that"
                                class="w-full h-32 object-cover rounded-xl border border-gray-200 dark:border-gray-700 mb-2">
                        @endif
                        <input type="file" id="wrong_image" name="wrong_image" accept="image/*"
                            class="form-input py-2 text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-primary-700 file:text-xs file:font-semibold dark:file:bg-primary-950/50 dark:file:text-primary-400 @error('wrong_image') border-red-400 @enderror">
                        @error('wrong_image')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('admin.dynamic_gallery.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-floppy-disk text-xs"></i> Update Image Set
            </button>
        </div>
    </form>
@endsection

@push('style')
    <style>
        .ck-editor__editable_inline {
            min-height: 200px;
        }

        .dark .ck.ck-editor__main > .ck-editor__editable {
            background: #1f2937;
            color: #e5e7eb;
        }

        .dark .ck.ck-toolbar {
            background: #111827;
            border-color: #374151;
        }
    </style>
@endpush

@push('script')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const description = document.querySelector('#description');
            if (description && typeof ClassicEditor !== 'undefined') {
                ClassicEditor.create(description)
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            description.value = editor.getData();
                        });
                    })
                    .catch(error => console.error(error));
            }
        });
    </script>
@endpush

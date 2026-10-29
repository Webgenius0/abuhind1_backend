@extends('backend.v2.app')

@section('title', 'Edit Page')

@section('content')
    <div class="ref-page">
        <!--begin: Page Header-->
        <div class="page-header">
            <div class="page-title">
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <a href="{{ route('admin.dynamic_page.index') }}" class="breadcrumb-link">Dynamic Pages</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-active">Edit Page</span>
                </nav>
                <p class="page-description">
                    Edit and update dynamic page content, title, and slug.
                </p>
            </div>
            <div class="page-actions flex items-center gap-2">
                <a href="{{ route('page.show', $data->page_slug) }}" target="_blank"
                    class="btn-cancel text-emerald-600 border-emerald-300 hover:bg-emerald-50">
                    <i class="fa-solid fa-eye"></i>
                    <span>View Public Page</span>
                </a>
                <a href="{{ route('admin.dynamic_page.index') }}" class="btn-primary">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Pages</span>
                </a>
            </div>
        </div>
        <!--end: Page Header-->

        <!--begin: Card-->
        <div class="card">
            <form action="{{ route('admin.dynamic_page.update', $data->id) }}" method="POST">
                @csrf

                <div class="grid grid-cols-12 gap-5">
                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="page_title" class="form-label"> Title <span class="text-red-500">*</span> </label>
                        <input type="text" name="page_title" id="page_title"
                            value="{{ old('page_title', $data->page_title) }}"
                            class="form-input @error('page_title') is-invalid @enderror" placeholder="Enter page title"
                            required>
                        @error('page_title')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="page_slug" class="form-label"> Slug </label>
                        <input type="text" id="page_slug" value="{{ $data->page_slug }}"
                            class="form-input bg-gray-100 dark:bg-gray-800/50 cursor-not-allowed"
                            placeholder="Page URL slug" readonly>
                    </div>

                    <div class="col-span-12 flex flex-col gap-1.5">
                        <label for="page_content" class="form-label"> Content <span class="text-red-500">*</span> </label>
                        <textarea class="form-input @error('page_content') is-invalid @enderror" id="page_content" name="page_content"
                            placeholder="Enter page content...">{{ old('page_content', $data->page_content) }}</textarea>
                        @error('page_content')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-actions mt-6">
                    <a href="{{ route('admin.dynamic_page.index') }}" class="btn-cancel"> Cancel </a>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> <span>Update Page</span>
                    </button>
                </div>
            </form>
        </div>
        <!--end: Card-->
    </div>
@endsection

@push('style')
    <style>
        .ck-editor__editable_inline {
            min-height: 350px;
        }

        .dark .ref-page .ck.ck-editor__main > .ck-editor__editable {
            background: #1f2937;
            color: #e5e7eb;
        }

        .dark .ref-page .ck.ck-toolbar {
            background: #111827;
            border-color: #374151;
        }
    </style>
@endpush

@push('script')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.querySelector('#page_content');
            if (content && typeof ClassicEditor !== 'undefined') {
                ClassicEditor.create(content).catch(error => console.error(error));
            }

            function slugify(text) {
                return text.toString().toLowerCase().trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            const title = document.getElementById('page_title');
            const slug = document.getElementById('page_slug');

            if (title && slug) {
                title.addEventListener('input', function() {
                    slug.value = slugify(this.value);
                });
            }
        });
    </script>
@endpush

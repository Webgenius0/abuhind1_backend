@extends('backend.v2.app')

@section('title', 'System Settings')

@section('content')
    <div class="ref-page">
        <!--begin: Page Header-->
        <div class="page-header">
            <div class="page-title">
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <a href="{{ route('admin.system.index') }}" class="breadcrumb-link">Settings</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-active">System Settings</span>
                </nav>
                <p class="page-description">
                    Manage global application settings, identity details, media assets, and maintenance mode parameters.
                </p>
            </div>
        </div>
        <!--end: Page Header-->

        <!--begin: System Settings Card-->
        <div class="card">
            <form id="system-setting-form" action="{{ route('admin.system.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <!--begin: Section Header-->
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div
                        class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-sliders text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">General Information</h3>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Configure core application title, contact
                            information, and assets</p>
                    </div>
                </div>
                <!--end: Section Header-->

                <!--begin: Form Fields-->
                <div class="grid grid-cols-12 gap-5">
                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="title" class="form-label">Application Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $setting->title ?? '') }}"
                            class="form-input @error('title') is-invalid @enderror" placeholder="Dashboard">
                        @error('title')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="system_name" class="form-label">System Name</label>
                        <input type="text" name="system_name" id="system_name"
                            value="{{ old('system_name', $setting->system_name ?? '') }}"
                            class="form-input @error('system_name') is-invalid @enderror" placeholder="Dashboard System">
                        @error('system_name')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="email" class="form-label">Support Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $setting->email ?? '') }}"
                            class="form-input @error('email') is-invalid @enderror" placeholder="support@example.com">
                        @error('email')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="phone" class="form-label">Support Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $setting->phone ?? '') }}"
                            class="form-input @error('phone') is-invalid @enderror" placeholder="+8801700000000">
                        @error('phone')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="timezone" class="form-label">Timezone</label>
                        <input type="text" name="timezone" id="timezone"
                            value="{{ old('timezone', $setting->timezone ?? 'Asia/Dhaka') }}"
                            class="form-input @error('timezone') is-invalid @enderror" placeholder="Asia/Dhaka">
                        @error('timezone')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="copyright_text" class="form-label">Copyright Text</label>
                        <input type="text" name="copyright_text" id="copyright_text"
                            value="{{ old('copyright_text', $setting->copyright_text ?? '') }}"
                            class="form-input @error('copyright_text') is-invalid @enderror"
                            placeholder="© 2026 Dashboard. All rights reserved.">
                        @error('copyright_text')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 flex flex-col gap-1.5">
                        <label for="address" class="form-label">Office Address</label>
                        <input type="text" name="address" id="address"
                            value="{{ old('address', $setting->address ?? '') }}"
                            class="form-input @error('address') is-invalid @enderror" placeholder="Dhaka, Bangladesh">
                        @error('address')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 flex flex-col gap-1.5">
                        <label for="description" class="form-label">System Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="form-input @error('description') is-invalid @enderror"
                            placeholder="Enter system description">{{ old('description', $setting->description ?? '') }}</textarea>
                        @error('description')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Maintenance Mode Section Header -->
                    <div class="col-span-12 mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-screwdriver-wrench text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-800 dark:text-white">Maintenance Mode</h3>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Temporarily restrict public access to the
                                platform during scheduled updates or system upgrades</p>
                        </div>
                    </div>

                    <!-- Maintenance Mode Toggle Card -->
                    <div class="col-span-12">
                        <div
                            class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">Activate System
                                    Maintenance</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    When enabled, standard visitors will see the maintenance announcement page while
                                    administrative access remains available.
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="maintenance_mode" value="1" id="maintenance_mode"
                                    class="sr-only peer"
                                    {{ old('maintenance_mode', $setting->maintenance_mode ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="col-span-12 flex flex-col gap-1.5">
                        <label for="maintenance_message" class="form-label">Maintenance Notice & Announcement</label>
                        <textarea name="maintenance_message" id="maintenance_message" rows="4"
                            class="form-input @error('maintenance_message') is-invalid @enderror"
                            placeholder="Enter maintenance announcement message for visitors...">{{ old('maintenance_message', $setting->maintenance_message ?? '') }}</textarea>
                        @error('maintenance_message')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <!--end: Form Fields-->

                <!--begin: Form Actions-->
                <div class="form-actions mt-6">
                    <a href="{{ route('admin.dashboard') }}" class="btn-cancel"> Cancel </a>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> <span>Save Settings</span>
                    </button>
                </div>
                <!--end: Form Actions-->
            </form>
        </div>
        <!--end: System Settings Card-->
    </div>
@endsection

@push('style')
    <style>
        .ck-editor__editable_inline {
            min-height: 220px;
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
            const maintenanceMessage = document.querySelector('#maintenance_message');
            if (maintenanceMessage && typeof ClassicEditor !== 'undefined') {
                ClassicEditor.create(maintenanceMessage)
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            maintenanceMessage.value = editor.getData();
                        });
                    })
                    .catch(error => console.error(error));
            }
        });
    </script>
@endpush

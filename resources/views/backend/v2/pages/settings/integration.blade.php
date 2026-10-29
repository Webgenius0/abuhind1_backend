@extends('backend.v2.app')

@section('title', 'Integration Settings')

@section('content')
    <div class="ref-page">
        <!--begin: Page Header-->
        <div class="page-header">
            <div class="page-title">
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <a href="{{ route('admin.integration.index') }}" class="breadcrumb-link">Settings</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-active">Integration Settings</span>
                </nav>
                <p class="page-description">
                    Manage API credentials for third-party services including Google OAuth, Apple Sign-In, and Google Gemini AI.
                </p>
            </div>
        </div>
        <!--end: Page Header-->

        <!--begin: Integration Card-->
        <div class="card">
            <!--begin: Card Header-->
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                <div
                    class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0 text-primary-600 dark:text-primary-400">
                    <i class="fa-solid fa-plug text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Third-Party Integrations</h3>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Manage API keys and credentials for external
                        services</p>
                </div>
            </div>
            <!--end: Card Header-->

            <!--begin: Tabs-->
            <div class="border-b border-gray-100 dark:border-gray-800 mb-6">
                <ul class="flex flex-wrap gap-1" id="integrationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all border-b-2 {{ $activeTab === 'google' ? 'active-tab text-primary-600 border-primary-500' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300' }}"
                            id="google-tab" type="button" role="tab" aria-controls="google"
                            aria-selected="{{ $activeTab === 'google' ? 'true' : 'false' }}"
                            onclick="switchTab('google', this)">
                            <i class="fa-brands fa-google me-1.5"></i>Google
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all border-b-2 {{ $activeTab === 'apple' ? 'active-tab text-primary-600 border-primary-500' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300' }}"
                            id="apple-tab" type="button" role="tab" aria-controls="apple"
                            aria-selected="{{ $activeTab === 'apple' ? 'true' : 'false' }}"
                            onclick="switchTab('apple', this)">
                            <i class="fa-brands fa-apple me-1.5"></i>Apple
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all border-b-2 {{ $activeTab === 'gemini' ? 'active-tab text-primary-600 border-primary-500' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300' }}"
                            id="gemini-tab" type="button" role="tab" aria-controls="gemini"
                            aria-selected="{{ $activeTab === 'gemini' ? 'true' : 'false' }}"
                            onclick="switchTab('gemini', this)">
                            <i class="fa-solid fa-wand-magic-sparkles me-1.5 text-primary-500"></i>Gemini AI
                        </button>
                    </li>
                </ul>
            </div>
            <!--end: Tabs-->

            <!--begin: Tab Content-->
            <div class="tab-content" id="integrationTabContent">
                <!--begin: Google Tab-->
                <div class="tab-pane fade {{ $activeTab === 'google' ? 'show active' : 'hidden' }}" id="google"
                    role="tabpanel" aria-labelledby="google-tab">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-red-500">
                                <i class="fa-brands fa-google text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white">Google OAuth Settings</h4>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Configure Google Sign-In credentials</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.integration.google') }}" autocomplete="off">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="_tab" value="google">

                            <div class="grid grid-cols-12 gap-5">
                                <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                                    <label for="GOOGLE_CLIENT_ID" class="form-label">
                                        Google Client ID <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="GOOGLE_CLIENT_ID" id="GOOGLE_CLIENT_ID"
                                        class="form-input @error('GOOGLE_CLIENT_ID') is-invalid @enderror"
                                        placeholder="Enter your Google Client ID"
                                        value="{{ old('GOOGLE_CLIENT_ID', env('GOOGLE_CLIENT_ID')) }}">
                                    @error('GOOGLE_CLIENT_ID')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                                    <label for="GOOGLE_CLIENT_SECRET" class="form-label">
                                        Google Client Secret <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative w-full">
                                        <input type="password" name="GOOGLE_CLIENT_SECRET" id="GOOGLE_CLIENT_SECRET"
                                            class="form-input pr-10 @error('GOOGLE_CLIENT_SECRET') is-invalid @enderror"
                                            placeholder="Enter your Google Client Secret" autocomplete="new-password"
                                            value="{{ old('GOOGLE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET')) }}">
                                        <button type="button" onclick="togglePassword('GOOGLE_CLIENT_SECRET', this)"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-primary-500 dark:hover:text-primary-400 focus:outline-none transition-colors cursor-pointer z-10"
                                            tabindex="-1" title="Show / Hide Client Secret">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                    @error('GOOGLE_CLIENT_SECRET')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-span-12 flex flex-col gap-1.5">
                                    <label for="GOOGLE_REDIRECT_URI" class="form-label">Google Redirect URI</label>
                                    <input type="text" name="GOOGLE_REDIRECT_URI" id="GOOGLE_REDIRECT_URI"
                                        class="form-input @error('GOOGLE_REDIRECT_URI') is-invalid @enderror"
                                        placeholder="http://localhost:8000/login/google/callback"
                                        value="{{ old('GOOGLE_REDIRECT_URI', env('GOOGLE_REDIRECT_URI')) }}">
                                    @error('GOOGLE_REDIRECT_URI')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="submit"
                                    class="btn-submit font-medium shadow-md shadow-primary-500/20 hover:shadow-lg hover:shadow-primary-500/35 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 cursor-pointer">
                                    <i class="fa-solid fa-floppy-disk text-xs"></i> <span>Save Google Settings</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end: Google Tab-->

                <!--begin: Apple Tab-->
                <div class="tab-pane fade {{ $activeTab === 'apple' ? 'show active' : 'hidden' }}" id="apple"
                    role="tabpanel" aria-labelledby="apple-tab">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-gray-800 dark:text-white">
                                <i class="fa-brands fa-apple text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white">Apple Sign-In Settings</h4>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Configure Apple Sign-In credentials</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.integration.apple') }}"
                            enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="_tab" value="apple">

                            <div class="grid grid-cols-12 gap-5">
                                <div class="col-span-12 md:col-span-4 flex flex-col gap-1.5">
                                    <label for="APPLE_CLIENT_ID" class="form-label">
                                        Apple Client ID <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="APPLE_CLIENT_ID" id="APPLE_CLIENT_ID"
                                        class="form-input @error('APPLE_CLIENT_ID') is-invalid @enderror"
                                        placeholder="Enter your Apple Client ID"
                                        value="{{ old('APPLE_CLIENT_ID', env('APPLE_CLIENT_ID')) }}" required>
                                    @error('APPLE_CLIENT_ID')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-span-12 md:col-span-4 flex flex-col gap-1.5">
                                    <label for="APPLE_TEAM_ID" class="form-label">
                                        Apple Team ID <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="APPLE_TEAM_ID" id="APPLE_TEAM_ID"
                                        class="form-input @error('APPLE_TEAM_ID') is-invalid @enderror"
                                        placeholder="Enter your Apple Team ID"
                                        value="{{ old('APPLE_TEAM_ID', env('APPLE_TEAM_ID')) }}" required>
                                    @error('APPLE_TEAM_ID')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-span-12 md:col-span-4 flex flex-col gap-1.5">
                                    <label for="APPLE_KEY_ID" class="form-label">
                                        Apple Key ID <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="APPLE_KEY_ID" id="APPLE_KEY_ID"
                                        class="form-input @error('APPLE_KEY_ID') is-invalid @enderror"
                                        placeholder="Enter your Apple Key ID"
                                        value="{{ old('APPLE_KEY_ID', env('APPLE_KEY_ID')) }}" required>
                                    @error('APPLE_KEY_ID')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                                    <div class="flex items-center justify-between">
                                        <label for="apple_key_file" class="form-label mb-0">
                                            Upload Private Key File (.p8)
                                        </label>
                                        @if (env('APPLE_PRIVATE_KEY'))
                                            <span
                                                class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded">
                                                <i class="fa-solid fa-check text-[10px]"></i> Current:
                                                {{ basename(env('APPLE_PRIVATE_KEY')) }}
                                            </span>
                                        @endif
                                    </div>
                                    <input type="file" name="apple_key_file" id="apple_key_file"
                                        class="form-input @error('apple_key_file') is-invalid @enderror"
                                        accept=".p8,.txt">
                                    <span class="text-xs text-gray-400">Upload your .p8 key file. It is saved to
                                        storage/app/private/ and linked to .env automatically.</span>
                                    @error('apple_key_file')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="submit"
                                    class="btn-submit font-medium shadow-md shadow-primary-500/20 hover:shadow-lg hover:shadow-primary-500/35 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 cursor-pointer">
                                    <i class="fa-solid fa-floppy-disk text-xs"></i> <span>Save Apple Settings</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end: Apple Tab-->

                <!--begin: Gemini Tab-->
                <div class="tab-pane fade {{ $activeTab === 'gemini' ? 'show active' : 'hidden' }}" id="gemini"
                    role="tabpanel" aria-labelledby="gemini-tab">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-950/50 flex items-center justify-center flex-shrink-0 text-primary-600 dark:text-primary-400">
                                <i class="fa-solid fa-wand-magic-sparkles text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white">Google Gemini AI Settings</h4>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Configure Google Gemini API key, model version, and API base endpoint</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.integration.gemini') }}" autocomplete="off">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="_tab" value="gemini">

                            <div class="grid grid-cols-12 gap-5">
                                <div class="col-span-12 flex flex-col gap-1.5">
                                    <label for="GEMINI_API_KEY" class="form-label">
                                        Gemini API Key <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative w-full">
                                        <input type="password" name="GEMINI_API_KEY" id="GEMINI_API_KEY"
                                            class="form-input pr-10 @error('GEMINI_API_KEY') is-invalid @enderror"
                                            placeholder="Enter your Gemini API Key (e.g. AQ.Ab8RN6Lh...)" autocomplete="new-password"
                                            value="{{ old('GEMINI_API_KEY', env('GEMINI_API_KEY')) }}">
                                        <button type="button" onclick="togglePassword('GEMINI_API_KEY', this)"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-primary-500 dark:hover:text-primary-400 focus:outline-none transition-colors cursor-pointer z-10"
                                            tabindex="-1" title="Show / Hide API Key">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                    <span class="text-xs text-gray-400">Your Google AI Studio API key.</span>
                                    @error('GEMINI_API_KEY')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                                    <label for="GEMINI_MODEL" class="form-label">
                                        Gemini Model <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="GEMINI_MODEL" id="GEMINI_MODEL"
                                        class="form-input @error('GEMINI_MODEL') is-invalid @enderror"
                                        placeholder="e.g. gemini-3.8-flash"
                                        value="{{ old('GEMINI_MODEL', env('GEMINI_MODEL', 'gemini-3.8-flash')) }}">
                                    <span class="text-xs text-gray-400">Target Gemini model (e.g. <code>gemini-3.8-flash</code>, <code>gemini-2.5-flash</code>, <code>gemini-1.5-pro</code>).</span>
                                    @error('GEMINI_MODEL')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                                    <label for="GEMINI_API_BASE" class="form-label">
                                        Gemini API Base URL
                                    </label>
                                    <input type="text" name="GEMINI_API_BASE" id="GEMINI_API_BASE"
                                        class="form-input @error('GEMINI_API_BASE') is-invalid @enderror"
                                        placeholder="https://generativelanguage.googleapis.com/v1beta"
                                        value="{{ old('GEMINI_API_BASE', env('GEMINI_API_BASE', 'https://generativelanguage.googleapis.com/v1beta')) }}">
                                    <span class="text-xs text-gray-400">Default: <code>https://generativelanguage.googleapis.com/v1beta</code></span>
                                    @error('GEMINI_API_BASE')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="submit"
                                    class="btn-submit font-medium shadow-md shadow-primary-500/20 hover:shadow-lg hover:shadow-primary-500/35 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 cursor-pointer">
                                    <i class="fa-solid fa-floppy-disk text-xs"></i> <span>Save Gemini Settings</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end: Gemini Tab-->
            </div>
            <!--end: Tab Content-->
        </div>
        <!--end: Integration Card-->
    </div>
@endsection

@push('script')
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId) || document.querySelector(`input[name="${inputId}"]`);
            if (!input) return;
            const icon = btn.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }

        function switchTab(tabId, btn) {
            localStorage.setItem('activeIntegrationTab', tabId);

            document.querySelectorAll('#integrationTabs button').forEach(t => {
                t.classList.remove('active-tab', 'text-primary-600', 'border-primary-500');
                t.classList.add('text-gray-500', 'border-transparent');
                t.setAttribute('aria-selected', 'false');
            });

            document.querySelectorAll('#integrationTabContent .tab-pane').forEach(p => {
                p.classList.add('hidden');
                p.classList.remove('show', 'active');
            });

            btn.classList.remove('text-gray-500', 'border-transparent');
            btn.classList.add('active-tab', 'text-primary-600', 'border-primary-500');
            btn.setAttribute('aria-selected', 'true');

            const target = document.getElementById(tabId);
            if (target) {
                target.classList.remove('hidden');
                target.classList.add('show', 'active');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const hasSessionTab = @json(session()->has('active_tab'));
            const activeTabPhp = "{{ $activeTab }}";

            let tabToActivate = activeTabPhp;
            if (!hasSessionTab && localStorage.getItem('activeIntegrationTab')) {
                const savedTab = localStorage.getItem('activeIntegrationTab');
                if (document.getElementById(savedTab + '-tab')) {
                    tabToActivate = savedTab;
                }
            }

            const btn = document.getElementById(tabToActivate + '-tab');
            if (btn) {
                switchTab(tabToActivate, btn);
            }
        });
    </script>
@endpush

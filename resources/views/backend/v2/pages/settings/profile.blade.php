@extends('backend.v2.app')

@section('title', 'My Profile')

@section('content')
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400">
                    <i class="fa-solid fa-user text-sm"></i>
                </span>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">My Profile</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Update your personal information, profile photo and password.
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Dashboard
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-300">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="profilePage()" class="space-y-4">

        <!-- Avatar card -->
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row items-center gap-5">
                <div class="relative">
                    @if (!empty(auth()->user()->avatar))
                        <img id="profile-picture" src="{{ asset(auth()->user()->avatar) }}" alt="Profile picture"
                            class="w-24 h-24 rounded-full object-cover ring-4 ring-white dark:ring-gray-800 shadow-md">
                    @else
                        <div id="profile-picture"
                            class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 text-white text-3xl font-bold flex items-center justify-center ring-4 ring-white dark:ring-gray-800 shadow-md">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Member since {{ optional(auth()->user()->created_at)->format('d M, Y') ?? '—' }}
                    </p>

                    <div class="mt-4 flex items-center justify-center sm:justify-start gap-3">
                        <input type="file" id="profile_picture_input" class="hidden"
                            accept="image/png, image/jpeg, image/jpg">
                        <label for="profile_picture_input" class="btn-primary cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i> Change Photo
                        </label>
                        <span x-show="uploading" x-cloak class="text-xs text-gray-400">Uploading…</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Update profile -->
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Update Profile</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Change the name and email on your account.</p>

                <form method="POST" action="{{ route('admin.update.profile') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="form-label" for="name">User Name</label>
                        <input type="text" id="name" name="name"
                            class="form-input @error('name') border-red-400 @enderror"
                            value="{{ old('name', auth()->user()->name) }}" placeholder="Full name">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email"
                            class="form-input @error('email') border-red-400 @enderror"
                            value="{{ old('email', auth()->user()->email) }}" placeholder="you@example.com">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary w-full">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Change password -->
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Change Password</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Use at least 8 characters for a strong password.</p>

                <form method="POST" action="{{ route('admin.update.Password') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="form-label" for="old_password">Current Password</label>
                        <input type="password" id="old_password" name="old_password"
                            class="form-input @error('old_password') border-red-400 @enderror"
                            placeholder="Current password">
                        @error('old_password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="password">New Password</label>
                        <input type="password" id="password" name="password"
                            class="form-input @error('password') border-red-400 @enderror" placeholder="New password">
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-input" placeholder="Confirm password">
                    </div>

                    <button type="submit" class="btn-primary w-full">
                        <i class="fa-solid fa-lock text-xs"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function profilePage() {
            return {
                uploading: false,
                init() {
                    const input = document.getElementById('profile_picture_input');
                    if (!input) return;

                    input.addEventListener('change', () => {
                        const file = input.files[0];
                        if (!file) return;

                        const formData = new FormData();
                        formData.append('profile_picture', file);
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                        this.uploading = true;

                        fetch('{{ route('admin.update.profile.picture') }}', {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    const img = document.getElementById('profile-picture');
                                    if (img.tagName === 'IMG') {
                                        img.src = data.image_url;
                                    } else {
                                        const replacement = document.createElement('img');
                                        replacement.id = 'profile-picture';
                                        replacement.src = data.image_url;
                                        replacement.className =
                                            'w-24 h-24 rounded-full object-cover ring-4 ring-white dark:ring-gray-800 shadow-md';
                                        img.replaceWith(replacement);
                                    }
                                } else {
                                    alert(data.message || 'Upload failed.');
                                }
                            })
                            .catch(() => alert('Something went wrong while uploading.'))
                            .finally(() => {
                                this.uploading = false;
                                input.value = '';
                            });
                    });
                }
            }
        }
    </script>
@endpush

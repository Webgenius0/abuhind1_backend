@extends('backend.v2.app')

@section('title', 'Create User')

@section('content')
    <div class="ref-page">
        <!--begin: Page Header-->
        <div class="page-header">
            <div class="page-title">
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <a href="{{ route('admin.users.index') }}" class="breadcrumb-link">Users</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-active">Create User</span>
                </nav>
                <p class="page-description">
                    Create a new user by filling out the form below. Ensure all required fields are completed before
                    submission.
                </p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.users.index') }}" class="btn-primary">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Users</span>
                </a>
            </div>
        </div>
        <!--end: Page Header-->

        <!--begin: Card-->
        <div class="card">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-12 gap-5">
                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="name" class="form-label"> Full Name <span class="text-red-500">*</span> </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="form-input @error('name') is-invalid @enderror" placeholder="Enter full name" required>
                        @error('name')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="email" class="form-label"> Email Address <span class="text-red-500">*</span> </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="form-input @error('email') is-invalid @enderror" placeholder="user@example.com" required>
                        @error('email')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="password" class="form-label"> Password <span class="text-red-500">*</span> </label>
                        <div class="relative w-full">
                            <input type="password" name="password" id="password"
                                class="form-input pr-10 @error('password') is-invalid @enderror"
                                placeholder="Min 8 characters" required>
                            <button type="button" onclick="togglePassword('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="role" class="form-label"> Role </label>
                        <select name="role" id="role" class="form-input @error('role') is-invalid @enderror">
                            <option value="User" @selected(old('role', 'User') === 'User')>User</option>
                            <option value="Admin" @selected(old('role') === 'Admin')>Admin</option>
                        </select>
                        @error('role')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 flex flex-col gap-1.5">
                        <label for="status" class="form-label"> Status </label>
                        <select name="status" id="status" class="form-input @error('status') is-invalid @enderror">
                            @foreach (['active', 'inactive', 'suspended', 'blocked'] as $status)
                                <option value="{{ $status }}" @selected(old('status', 'active') === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn-cancel"> Cancel </a>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-user-plus text-xs"></i> <span>Create User</span>
                    </button>
                </div>
            </form>
        </div>
        <!--end: Card-->
    </div>
@endsection

@push('script')
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
@endpush

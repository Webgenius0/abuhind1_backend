@extends('backend.v2.app')

@section('title', 'Edit Premium Code')

@section('content')
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400">
                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                </span>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Edit Premium Code</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Updating an unused code will resend it when assigned to a user.
            </p>
        </div>
        <a href="{{ route('admin.premium_code.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Codes
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

    <form method="POST" action="{{ route('admin.premium_code.update', $code->id) }}">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="card p-5 xl:col-span-2">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Code Details</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Update the code, duration and expiry.</p>

                <div class="space-y-4">
                    <div>
                        <label class="form-label" for="code">Promo Code</label>
                        <input type="text" id="code" name="code" required
                            class="form-input font-mono @error('code') border-red-400 @enderror"
                            value="{{ old('code', $code->code) }}">
                        @error('code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="validity_days">Premium Duration (days)</label>
                            <input type="number" id="validity_days" name="validity_days" min="1" max="3650"
                                class="form-input @error('validity_days') border-red-400 @enderror"
                                placeholder="Leave empty for lifetime"
                                value="{{ old('validity_days', $code->validity_days) }}">
                            @error('validity_days')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label" for="code_expires_at">Code Expires At <span
                                    class="text-xs font-normal text-gray-400">(optional)</span></label>
                            <input type="datetime-local" id="code_expires_at" name="code_expires_at"
                                class="form-input @error('code_expires_at') border-red-400 @enderror"
                                value="{{ old('code_expires_at', $code->code_expires_at ? $code->code_expires_at->format('Y-m-d\TH:i') : '') }}">
                            @error('code_expires_at')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Assignment</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Change who this code belongs to.</p>

                <div class="space-y-4">
                    <div>
                        <label class="form-label" for="user_id">Assign to User <span
                                class="text-xs font-normal text-gray-400">(optional)</span></label>
                        <select id="user_id" name="user_id" class="form-input @error('user_id') border-red-400 @enderror">
                            <option value="">— Not assigned (anyone can redeem) —</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    @selected((string) old('user_id', $code->assigned_user_id) === (string) $user->id)>
                                    {{ $user->email }} ({{ $user->name }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">
                            Saving sends the promo code details to the selected email address.
                        </p>
                        @error('user_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="note">Note <span
                                class="text-xs font-normal text-gray-400">(optional)</span></label>
                        <input type="text" id="note" name="note" maxlength="255"
                            class="form-input @error('note') border-red-400 @enderror"
                            value="{{ old('note', $code->note) }}">
                        @error('note')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('admin.premium_code.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-floppy-disk text-xs"></i> Update Code
            </button>
        </div>
    </form>
@endsection

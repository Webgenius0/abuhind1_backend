@extends('backend.v2.app')

@section('title', 'Generate Premium Codes')

@section('content')
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400">
                    <i class="fa-solid fa-ticket text-sm"></i>
                </span>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Generate Premium Codes</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Create a custom code or let the system generate one automatically.
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

    <form method="POST" action="{{ route('admin.premium_code.store') }}">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="card p-5 xl:col-span-2">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Code Details</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Configure how the code is created and redeemed.</p>

                <div class="space-y-4">
                    <div>
                        <label class="form-label" for="code">Custom Code <span
                                class="text-xs font-normal text-gray-400">(optional)</span></label>
                        <input type="text" id="code" name="code"
                            class="form-input @error('code') border-red-400 @enderror" placeholder="e.g. PRM-VIP-2026"
                            value="{{ old('code') }}">
                        <p class="mt-1 text-xs text-gray-400">Leave empty to auto-generate codes.</p>
                        @error('code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="count">Number of Codes</label>
                        <input type="number" id="count" name="count" min="1" max="500"
                            class="form-input @error('count') border-red-400 @enderror"
                            value="{{ old('count', 1) }}">
                        <p class="mt-1 text-xs text-gray-400">Ignored when a specific user is selected below.</p>
                        @error('count')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="validity_days">Premium Duration (days)</label>
                            <input type="number" id="validity_days" name="validity_days" min="1" max="3650"
                                class="form-input @error('validity_days') border-red-400 @enderror"
                                placeholder="Leave empty for lifetime" value="{{ old('validity_days') }}">
                            @error('validity_days')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label" for="code_expires_at">Code Expires At <span
                                    class="text-xs font-normal text-gray-400">(optional)</span></label>
                            <input type="datetime-local" id="code_expires_at" name="code_expires_at"
                                class="form-input @error('code_expires_at') border-red-400 @enderror"
                                value="{{ old('code_expires_at') }}">
                            @error('code_expires_at')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Assignment</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Optionally send the code directly to a user.</p>

                <div class="space-y-4">
                    <div>
                        <label class="form-label" for="user_id">Assign to User <span
                                class="text-xs font-normal text-gray-400">(optional)</span></label>
                        <select id="user_id" name="user_id" class="form-input @error('user_id') border-red-400 @enderror">
                            <option value="">— Not assigned (anyone can redeem) —</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>
                                    {{ $user->email }} ({{ $user->name }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">
                            When selected, exactly one code is generated and emailed to this address.
                        </p>
                        @error('user_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="note">Note <span
                                class="text-xs font-normal text-gray-400">(optional)</span></label>
                        <input type="text" id="note" name="note" maxlength="255"
                            class="form-input @error('note') border-red-400 @enderror" value="{{ old('note') }}">
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
                <i class="fa-solid fa-wand-magic-sparkles text-xs"></i> Generate
            </button>
        </div>
    </form>
@endsection

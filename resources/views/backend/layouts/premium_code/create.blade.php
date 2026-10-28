@extends('backend.app')
@section('title', 'Generate Premium Code')
@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Generate Premium Codes</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.premium_code.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Custom code (optional)</label>
                                    <input type="text" name="code" class="form-control"
                                        placeholder="e.g. PRM-VIP-2026" value="{{ old('code') }}">
                                    <small class="text-muted">Type your own custom code here, or leave empty to auto-generate.</small>
                                    @error('code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Assign to a user / email (optional)</label>
                                    <select name="user_id" class="form-select">
                                        <option value="">— Not assigned (anyone can redeem) —</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->email }} ({{ $user->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">When a user is selected, exactly 1 promo code will be generated and automatically emailed to their address.</small>
                                    @error('user_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Number of codes to generate</label>
                                    <input type="number" name="count" class="form-control" min="1" max="500"
                                        value="{{ old('count', 1) }}">
                                    <small class="text-muted">Ignored if a specific user is selected above (generates exactly 1 code for them).</small>
                                    @error('count')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Premium duration (days)</label>
                                    <input type="number" name="validity_days" class="form-control" min="1"
                                        max="3650" placeholder="Leave empty for lifetime" value="{{ old('validity_days') }}">
                                    @error('validity_days')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Code expires at (optional)</label>
                                    <input type="datetime-local" name="code_expires_at" class="form-control" value="{{ old('code_expires_at') }}">
                                    <small class="text-muted">If left empty, the code never expires until it is redeemed.</small>
                                    @error('code_expires_at')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Note (optional)</label>
                                    <input type="text" name="note" class="form-control" maxlength="255" value="{{ old('note') }}">
                                    @error('note')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Generate</button>
                                <a href="{{ route('admin.premium_code.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

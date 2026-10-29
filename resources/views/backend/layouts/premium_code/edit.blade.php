@extends('backend.app')
@section('title', 'Edit Premium Code')
@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Edit Premium Code: {{ $code->code }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.premium_code.update', $code->id) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Promo Code</label>
                                    <input type="text" name="code" class="form-control"
                                        placeholder="e.g. PRM-VIP-2026" value="{{ old('code', $code->code) }}" required>
                                    @error('code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Assign to a user / email (optional)</label>
                                    <select name="user_id" class="form-select">
                                        <option value="">— Not assigned (anyone can redeem) —</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ (string) old('user_id', $code->assigned_user_id) === (string) $user->id ? 'selected' : '' }}>
                                                {{ $user->email }} ({{ $user->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select an email to assign this promo code. Saving will automatically send an email to that address with the promo code details.</small>
                                    @error('user_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Premium duration (days)</label>
                                    <input type="number" name="validity_days" class="form-control" min="1"
                                        max="3650" placeholder="Leave empty for lifetime" value="{{ old('validity_days', $code->validity_days) }}">
                                    @error('validity_days')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Code expires at (optional)</label>
                                    <input type="datetime-local" name="code_expires_at" class="form-control"
                                        value="{{ old('code_expires_at', $code->code_expires_at ? $code->code_expires_at->format('Y-m-d\TH:i') : '') }}">
                                    <small class="text-muted">If left empty, the code never expires until it is redeemed.</small>
                                    @error('code_expires_at')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Note (optional)</label>
                                    <input type="text" name="note" class="form-control" maxlength="255" value="{{ old('note', $code->note) }}">
                                    @error('note')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('admin.premium_code.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@extends('backend.app')
@section('title', 'Premium Codes')
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
                                    <small class="text-muted">Type your own code here, or leave empty to auto-generate
                                        one/many below.</small>
                                    @error('code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- <div class="mb-3">
                                    <label class="form-label">Number of codes to generate</label>
                                    <input type="number" name="count" class="form-control" min="1" max="500"
                                        value="1">
                                    <small class="text-muted">Ignored if a specific user is selected below (generates
                                        exactly 1 code for
                                        them).</small>
                                    @error('count')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div> --}}

                                <div class="mb-3">
                                    <label class="form-label">Assign to a specific user (optional)</label>
                                    <select name="user_id" class="form-select">
                                        <option value="">— Not assigned (anyone can redeem) —</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Premium duration (days)</label>
                                    <input type="number" name="validity_days" class="form-control" min="1"
                                        max="3650" placeholder="Leave empty for lifetime">
                                    @error('validity_days')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Code expires at (optional)</label>
                                    <input type="datetime-local" name="code_expires_at" class="form-control">
                                    <small class="text-muted">If left empty, the code never expires until it's used.</small>
                                    @error('code_expires_at')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Note (optional)</label>
                                    <input type="text" name="note" class="form-control" maxlength="255">
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

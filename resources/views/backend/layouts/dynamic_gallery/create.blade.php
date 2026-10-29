@extends('backend.app')

@section('title', 'Add Dynamic Image')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-style mb-4">
                        <div class="card card-body">
                            <h4 class="mb-4">Add Dynamic Image</h4>
                            <form method="POST" action="{{ route('admin.dynamic_gallery.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="input-style-1 mt-3">
                                    <label for="type">Option / Type:</label>
                                    @php
                                        $hasFront = in_array('front', $existingTypes ?? []);
                                        $hasSide  = in_array('side', $existingTypes ?? []);
                                        $defaultType = $hasFront ? 'side' : 'front';
                                    @endphp
                                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                                        <option value="front" {{ old('type', $defaultType) == 'front' ? 'selected' : '' }} {{ $hasFront ? 'disabled' : '' }}>
                                            Front View {{ $hasFront ? '(Already created)' : '' }}
                                        </option>
                                        <option value="side" {{ old('type', $defaultType) == 'side' ? 'selected' : '' }} {{ $hasSide ? 'disabled' : '' }}>
                                            Side View {{ $hasSide ? '(Already created)' : '' }}
                                        </option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="input-style-1 mt-3">
                                    <label for="title">Title:</label>
                                    <input type="text" placeholder="Enter Title" id="title"
                                        class="form-control @error('title') is-invalid @enderror" name="title"
                                        value="{{ old('title') }}" />
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="input-style-1 mt-3">
                                    <label for="subtitle">Subtitle:</label>
                                    <input type="text" placeholder="e.g. FRONT VIEW or SIDE VIEW" id="subtitle"
                                        class="form-control @error('subtitle') is-invalid @enderror" name="subtitle"
                                        value="{{ old('subtitle') }}" />
                                    @error('subtitle')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="input-style-1 mt-3">
                                    <label for="description">Description:</label>
                                    <textarea placeholder="Type description here..." id="description" name="description" rows="4"
                                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="input-style-1 mt-3">
                                    <label for="right_image">That's Right Image (Correct):</label>
                                    <input type="file" id="right_image" class="form-control @error('right_image') is-invalid @enderror"
                                        name="right_image" accept="image/*" />
                                    @error('right_image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="input-style-1 mt-3">
                                    <label for="wrong_image">Not Like That Image (Incorrect):</label>
                                    <input type="file" id="wrong_image" class="form-control @error('wrong_image') is-invalid @enderror"
                                        name="wrong_image" accept="image/*" />
                                    @error('wrong_image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary px-4">Save</button>
                                    <a href="{{ route('admin.dynamic_gallery.index') }}"
                                        class="btn btn-danger px-4 ms-2">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

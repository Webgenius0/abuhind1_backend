@extends('backend.app')

@section('title', 'Add Video Tutorial')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-style mb-4">
                        <div class="card card-body">
                            <h4 class="mb-4">Add New Video Tutorial</h4>
                            <form method="POST" action="{{ route('admin.video_tutorial.store') }}">
                                @csrf
                                <div class="input-style-1 mt-3">
                                    <label for="title">Title:</label>
                                    <input type="text" placeholder="Enter Video Title" id="title"
                                        class="form-control @error('title') is-invalid @enderror" name="title"
                                        value="{{ old('title') }}" />
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="input-style-1 mt-3">
                                    <label for="youtube_link">YouTube Link:</label>
                                    <input type="url" placeholder="https://www.youtube.com/watch?v=..."
                                        id="youtube_link" class="form-control @error('youtube_link') is-invalid @enderror"
                                        name="youtube_link" value="{{ old('youtube_link') }}" />
                                    @error('youtube_link')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="input-style-1 mt-3">
                                    <label for="description">Description:</label>
                                    <textarea placeholder="Type short description here..." id="description" name="description" rows="4"
                                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ route('admin.video_tutorial.index') }}"
                                        class="btn btn-danger me-2">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

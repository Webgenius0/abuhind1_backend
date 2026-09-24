@extends('backend.app')

@section('title', 'Update Dynamic Page')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-style mb-4">
                        <div class="card card-body">
                            <form method="POST" action="{{ route('admin.dynamic_page.update', ['id' => $data->id]) }}">
                                @csrf
                                <div class="input-style-1 mt-4">
                                    <label for="page_title">Title:</label>
                                    <input type="text" placeholder="Enter Title" id="page_title"
                                        class="form-control @error('page_title') is-invalid @enderror" name="page_title"
                                        value="{{ old('page_title', $data->page_title) }}" />
                                    @error('page_title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="input-style-1 mt-4">
                                    <label for="page_content">Content:</label>

                                    <!-- FAQ Instruction Note -->
                                    <div class="alert alert-warning py-2 mb-2" style="font-size: 14px;">
                                        <strong>💡 FAQ Writing Instructions:</strong>
                                        <ul class="mb-0 ps-3">
                                            <li>Use <code>&lt;h3&gt;</code> or <code>&lt;h4&gt;</code> tags for each
                                                question (e.g., <code>&lt;h3&gt;1. What is this service?&lt;/h3&gt;</code>).
                                            </li>
                                            <li>Use a <code>&lt;p&gt;</code> tag immediately following the question for the
                                                answer (e.g., <code>&lt;p&gt;This service is designed...&lt;/p&gt;</code>).
                                            </li>
                                        </ul>
                                    </div>

                                    <textarea placeholder="Type here..." id="page_content" name="page_content"
                                        class="form-control @error('page_content') is-invalid @enderror">
                                    {{ old('page_content', $data->page_content) }}
                                    </textarea>
                                    @error('page_content')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ route('admin.dynamic_page.index') }}" class="btn btn-danger me-2">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        ClassicEditor
            .create(document.querySelector('#page_content'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush

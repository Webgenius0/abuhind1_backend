@extends('backend.app')

@section('title', 'Video Tutorials')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Video Tutorials List</h5>
                            <a href="{{ route('admin.video_tutorial.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add New Video
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="videoTable">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>YouTube Link</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#videoTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.video_tutorial.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'youtube_link',
                        name: 'youtube_link',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<div class="btn-group btn-group-sm" role="group">
                                <a href="/admin/video-tutorial/edit/${row.id}" class="text-white btn btn-primary" title="Edit">
                                   <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                                <button onclick="showDeleteConfirm(${row.id})" class="btn btn-danger text-white" title="Delete">
                                   <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>`;
                        }
                    }
                ]
            });
        });

        // Delete Confirmation & Ajax Request
        function showDeleteConfirm(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "/admin/video-tutorial/destroy/" + id,
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#videoTable').DataTable().ajax.reload();
                            if (response.message) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                            }
                        },
                        error: function(error) {
                            Swal.fire(
                                'Failed!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endpush

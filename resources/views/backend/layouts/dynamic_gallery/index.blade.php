@extends('backend.app')

@section('title', 'Dynamic Images')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Dynamic Images List</h5>
                            @if($canCreate)
                                <a href="{{ route('admin.dynamic_gallery.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus me-1"></i> Add New
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="galleryTable">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Type</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>That's Right</th>
                                            <th>Not Like That</th>
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
            $('#galleryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.dynamic_gallery.index') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type'
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
                        data: 'right_image',
                        name: 'right_image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'wrong_image',
                        name: 'wrong_image',
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
                        searchable: false
                    }
                ]
            });
        });

        // Status Change Alert
        function showStatusChangeAlert(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to change status of this item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '{{ route('admin.dynamic_gallery.status', ':id') }}';
                    url = url.replace(':id', id);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(resp) {
                            toastr.success(resp.message || 'Status updated');
                            $('#galleryTable').DataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('Failed to change status');
                        }
                    });
                }
            });
        }

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
                    let url = '{{ route('admin.dynamic_gallery.destroy', ':id') }}';
                    url = url.replace(':id', id);
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#galleryTable').DataTable().ajax.reload();
                            if (response.message) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                            }
                        },
                        error: function() {
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

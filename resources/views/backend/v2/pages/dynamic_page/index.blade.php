@extends('backend.v2.app')

@section('title', 'Dynamic Pages')

@section('content')
    <div class="ref-page">
        <!--begin: Page Header-->
        <div class="page-header">
            <div class="page-title">
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-active">Dynamic Pages</span>
                </nav>
                <p class="page-description">
                    Dynamic Pages Management: View, search, and manage all dynamic pages in the system.
                </p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.dynamic_page.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Add Page</span>
                </a>
            </div>
        </div>
        <!--end: Page Header-->

        <!--begin: Table Card-->
        <div class="card">
            <table id="pages-table" class="table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Content</th>
                        <th class="text-center">Status</th>
                        <th>Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Populated via AJAX DataTables --}}
                </tbody>
            </table>
        </div>
        <!--end: Table Card-->
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#pages-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.dynamic_page.index') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'page_title',
                        name: 'page_title',
                    },
                    {
                        data: 'page_slug',
                        name: 'page_slug',
                    },
                    {
                        data: 'page_content',
                        name: 'page_content',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [],
                language: {
                    paginate: {
                        previous: '<i class="fa-solid fa-chevron-left"></i>',
                        next: '<i class="fa-solid fa-chevron-right"></i>'
                    }
                }
            });
        });

        // Toggle Status via AJAX with SweetAlert Confirmation
        function changeStatus(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Change status?',
                text: 'Are you sure you want to toggle this status?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Change',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.dynamic_page.status', ':id') }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Status updated successfully.');
                            $('#pages-table').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to update status.');
                            $('#pages-table').DataTable().ajax.reload(null, false);
                        }
                    });
                } else {
                    $('#pages-table').DataTable().ajax.reload(null, false);
                }
            });
        }

        // Delete Record via AJAX with SweetAlert Confirmation
        function deleteRecord(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.dynamic_page.destroy', ':id') }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response['t-success'] || response.success) {
                                toastr.success(response.message || 'Deleted successfully.');
                                $('#pages-table').DataTable().ajax.reload(null, false);
                            } else {
                                toastr.error(response.message || 'Failed to delete record.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                        }
                    });
                }
            });
        }
    </script>
@endpush

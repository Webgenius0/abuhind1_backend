@extends('backend.v2.app')

@section('title', 'Video Tutorials')

@section('content')
    <div class="ref-page">
        <!--begin: Page Header-->
        <div class="page-header">
            <div class="page-title">
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-active">Video Tutorials</span>
                </nav>
                <p class="page-description">
                    Video Tutorials Management: View, search, and manage all tutorial videos in the system.
                </p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.video_tutorial.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Add Video</span>
                </a>
            </div>
        </div>
        <!--end: Page Header-->

        <!--begin: Table Card-->
        <div class="card">
            <table id="videoTable" class="table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>YouTube Link</th>
                        <th class="text-center">Status</th>
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
            const table = $('#videoTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.video_tutorial.index') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'description',
                        name: 'description',
                    },
                    {
                        data: 'youtube_link',
                        name: 'youtube_link',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
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
                        url: '{{ route('admin.video_tutorial.status', ':id') }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Status updated successfully.');
                            $('#videoTable').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to update status.');
                            $('#videoTable').DataTable().ajax.reload(null, false);
                        }
                    });
                } else {
                    $('#videoTable').DataTable().ajax.reload(null, false);
                }
            });
        }

        // Delete Record via AJAX with SweetAlert Confirmation
        function deleteRecord(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This video tutorial will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.video_tutorial.destroy', ':id') }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response['t-success'] || response.success) {
                                toastr.success(response.message || 'Deleted successfully.');
                                $('#videoTable').DataTable().ajax.reload(null, false);
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

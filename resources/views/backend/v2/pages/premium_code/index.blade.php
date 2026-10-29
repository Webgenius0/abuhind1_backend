@extends('backend.v2.app')

@section('title', 'Premium Codes')

@section('content')
    <div class="ref-page">
        <!--begin: Page Header-->
        <div class="page-header">
            <div class="page-title">
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-active">Premium Codes</span>
                </nav>
                <p class="page-description">
                    Premium Codes Management: Generate, assign and manage promo codes that unlock premium access.
                </p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.premium_code.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Add Premium</span>
                </a>
            </div>
        </div>
        <!--end: Page Header-->

        <!--begin: Table Card-->
        <div class="card">
            <!-- Filter Row -->
            <div class="flex items-center justify-end mb-3">
                <div class="flex items-center gap-2">
                    <label for="filter-status" class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Filter Status:</label>
                    <select id="filter-status"
                        class="px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="">All Status</option>
                        <option value="unused">Unused</option>
                        <option value="used">Used</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
            </div>

            <table id="premium-code-table" class="table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Code</th>
                        <th>Assigned To</th>
                        <th>Validity</th>
                        <th class="text-center">Status</th>
                        <th>Used By</th>
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
            const table = $('#premium-code-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.premium_code.index') }}",
                    data: function(d) {
                        d.status = $('#filter-status').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'code_info',
                        name: 'code_info',
                    },
                    {
                        data: 'assigned_to',
                        name: 'assigned_to',
                        searchable: false,
                    },
                    {
                        data: 'validity',
                        name: 'validity_days',
                        searchable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'used_by_user',
                        name: 'used_by_user',
                        searchable: false,
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

            $('#filter-status').on('change', function() {
                table.ajax.reload();
            });
        });

        // Toggle Status via AJAX with SweetAlert Confirmation
        function changeStatus(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Change status?',
                text: 'Are you sure you want to toggle this promo code status?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Change',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.premium_code.status', ':id') }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Status updated successfully.');
                            $('#premium-code-table').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to update code status.');
                            $('#premium-code-table').DataTable().ajax.reload(null, false);
                        }
                    });
                } else {
                    $('#premium-code-table').DataTable().ajax.reload(null, false);
                }
            });
        }

        // Delete Record via AJAX with SweetAlert Confirmation
        function deleteRecord(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This premium code will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.premium_code.destroy', ':id') }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response['t-success'] || response.success) {
                                toastr.success(response.message || 'Deleted successfully.');
                                $('#premium-code-table').DataTable().ajax.reload(null, false);
                            } else {
                                toastr.error(response.message || 'Failed to delete code.');
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

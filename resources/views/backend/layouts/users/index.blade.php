@extends('backend.app')

@section('title', 'User Management')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">User Management</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="users-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th style="width: 60px;">Image</th>
                                            <th>User ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Plan</th>
                                            <th style="width: 70px;">Status</th>
                                            <th>Joined Date</th>
                                            <th style="width: 90px;">Action</th>
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

    <!-- Simple Minimalist User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2 px-3 border-bottom">
                    <h6 class="modal-title fw-bold" id="userDetailsModalLabel">User Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <img id="modal-avatar" src="{{ asset('backend/images/profile.png') }}" alt="Avatar"
                             class="rounded-circle me-3 border"
                             style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 id="modal-name" class="mb-0 fw-bold">User Name</h6>
                            <span id="modal-user-id" class="text-muted small">USR-XXXXXX</span>
                        </div>
                    </div>

                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-normal py-1" style="width: 40%;">Email:</th>
                                <td id="modal-email" class="fw-medium text-dark py-1">user@example.com</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Email Verified:</th>
                                <td id="modal-email-verified" class="fw-medium text-dark py-1">No</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Role:</th>
                                <td id="modal-role" class="fw-medium text-dark py-1">User</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Status:</th>
                                <td id="modal-status" class="fw-medium text-dark py-1">Active</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Plan:</th>
                                <td id="modal-plan" class="fw-medium text-dark py-1">Free</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Plan Expiry:</th>
                                <td id="modal-premium-expires" class="fw-medium text-dark py-1">N/A</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Gender:</th>
                                <td id="modal-gender" class="fw-medium text-dark py-1">N/A</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Background:</th>
                                <td id="modal-background" class="fw-medium text-dark py-1">N/A</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">18+ Confirmed:</th>
                                <td id="modal-age-verified" class="fw-medium text-dark py-1">No</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Auth Provider:</th>
                                <td id="modal-provider" class="fw-medium text-dark py-1">Direct</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Face Scans:</th>
                                <td id="modal-scans-count" class="fw-medium text-dark py-1">0</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal py-1">Joined Date:</th>
                                <td id="modal-created-at" class="fw-medium text-dark py-1">N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer py-2 px-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.users.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'avatar', name: 'avatar', orderable: false, searchable: false },
                    { data: 'user_id', name: 'user_id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role' },
                    { data: 'plan', name: 'is_premium' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'created_at_formatted', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        // Status Change Alert & Ajax
        function showStatusChangeAlert(id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to update this user status?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    statusChange(id);
                } else {
                    $('#users-table').DataTable().ajax.reload(null, false);
                }
            });
        }

        function statusChange(id) {
            let url = '{{ route('admin.users.status', ':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function(resp) {
                    $('#users-table').DataTable().ajax.reload(null, false);
                    if (resp.message) {
                        Swal.fire({
                            title: 'Updated!',
                            text: resp.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function() {
                    $('#users-table').DataTable().ajax.reload(null, false);
                    Swal.fire('Failed!', 'Unable to update status.', 'error');
                }
            });
        }

        // Show User Details Modal
        function showUserDetails(id) {
            let url = '{{ route('admin.users.show', ':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function(resp) {
                    if (resp.success && resp.data) {
                        const u = resp.data;
                        $('#modal-avatar').attr('src', u.avatar);
                        $('#modal-name').text(u.name);
                        $('#modal-user-id').text(u.user_id);
                        $('#modal-email').text(u.email);
                        $('#modal-email-verified').text(u.email_verified_at);
                        $('#modal-role').text(u.role);
                        $('#modal-status').text(u.status);
                        $('#modal-plan').text(u.plan);
                        $('#modal-premium-expires').text(u.premium_expires_at);
                        $('#modal-gender').text(u.gender);
                        $('#modal-background').text(u.background);
                        $('#modal-age-verified').text(u.is_18_plus);
                        $('#modal-provider').text(u.provider);
                        $('#modal-scans-count').text(u.face_scans_count);
                        $('#modal-created-at').text(u.created_at);

                        $('#userDetailsModal').modal('show');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Unable to fetch user details.', 'error');
                }
            });
        }

        // Delete User Confirmation & Ajax
        function showDeleteConfirm(id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This user account will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '{{ route('admin.users.destroy', ':id') }}';
                    $.ajax({
                        type: "DELETE",
                        url: url.replace(':id', id),
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(resp) {
                            $('#users-table').DataTable().ajax.reload(null, false);
                            if (resp.success) {
                                Swal.fire('Deleted!', resp.message, 'success');
                            } else {
                                Swal.fire('Error!', resp.message, 'error');
                            }
                        },
                        error: function(err) {
                            $('#users-table').DataTable().ajax.reload(null, false);
                            const msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Something went wrong.';
                            Swal.fire('Failed!', msg, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush

@extends('backend.v2.app')

@section('title', 'User Management')

@section('content')
    <div class="ref-page">
        <!--begin: Page Header-->
        <div class="page-header">
            <!--begin: Page Title-->
            <div class="page-title">
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-active">Users</span>
                </nav>
                <p class="page-description">
                    User Management: View, search, and manage all users in the system.
                </p>
            </div>
            <!--end: Page Title-->
            <!--begin: Actions-->
            <div class="page-actions">
                <a href="{{ route('admin.users.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Add User</span>
                </a>
            </div>
            <!--end: Actions-->
        </div>
        <!--end: Page Header-->

        <!--begin: Table Card-->
        <div class="card">
            <table id="users-table" class="table">
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-center">Status</th>
                        <th>Joined</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- The table body will be populated by DataTables via AJAX --}}
                </tbody>
            </table>
        </div>
        <!--end: Table Card-->

        <!--begin: User Details Modal-->
        <div id="userModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="um_heading">
            <!--begin: Backdrop-->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeUserModal()"></div>
            <!--end: Backdrop-->

            <!--begin: Dialog-->
            <div class="relative flex items-center justify-center min-h-screen p-4 pointer-events-none">
                <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden pointer-events-auto">
                    <!--begin: Header-->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 id="um_heading" class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-user text-primary-500"></i>
                            <span>User Details</span>
                        </h3>
                        <button type="button" onclick="closeUserModal()"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                            title="Close">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <!--end: Header-->

                    <!--begin: Body-->
                    <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">
                        <!-- User Avatar & Header Info -->
                        <div class="flex items-center gap-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <div id="um_avatar_container" class="relative">
                                <img id="um_avatar" src="" alt="Avatar"
                                    class="w-16 h-16 rounded-full object-cover border-2 border-primary-500/20">
                                <div id="um_avatar_placeholder"
                                    class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-user text-2xl"></i>
                                </div>
                            </div>
                            <div>
                                <h4 id="um_name" class="text-lg font-bold text-gray-800 dark:text-gray-100">-</h4>
                                <p id="um_email" class="text-xs text-gray-500 dark:text-gray-400">-</p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span id="um_role"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">-</span>
                                    <span id="um_status"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">User ID</label>
                                <p id="um_user_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Subscription Plan</label>
                                <p id="um_plan" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Plan Expiry</label>
                                <p id="um_plan_expiry" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Email Verified</label>
                                <p id="um_email_verified" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Gender</label>
                                <p id="um_gender" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Background</label>
                                <p id="um_background" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">18+ Confirmed</label>
                                <p id="um_18_plus" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Auth Provider</label>
                                <p id="um_provider" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                        </div>

                        <!-- Joined Date -->
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Joined Date</label>
                            <p id="um_created_at" class="text-sm text-gray-700 dark:text-gray-300">-</p>
                        </div>
                    </div>
                    <!--end: Body-->

                    <!--begin: Footer-->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <button type="button" onclick="closeUserModal()" class="btn-secondary">Close</button>
                    </div>
                    <!--end: Footer-->
                </div>
            </div>
            <!--end: Dialog-->
        </div>
        <!--end: User Details Modal-->

        <!--begin: Edit User Modal-->
        <div id="editUserModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="editUserHeading">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeEditUserModal()"></div>
            <div class="relative flex items-center justify-center min-h-screen p-4 pointer-events-none">
                <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden pointer-events-auto">
                    <form id="editUserForm" onsubmit="submitEditUser(event)">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_user_id">

                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-primary-500/10 text-primary-600 flex items-center justify-center">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </div>
                                <div>
                                    <h3 id="editUserHeading" class="text-sm font-bold text-gray-800 dark:text-gray-100">
                                        Edit User Account
                                    </h3>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        Update user profile and account details
                                    </p>
                                </div>
                            </div>
                            <button type="button" onclick="closeEditUserModal()"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                title="Close">
                                <i class="fa-solid fa-xmark text-base"></i>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="edit_user_name" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="edit_user_email" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                        Role <span class="text-red-500">*</span>
                                    </label>
                                    <select id="edit_user_role" required
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                        <option value="User">User</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select id="edit_user_status" required
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                    New Password <span class="text-gray-400 normal-case font-normal">(Leave blank to keep current)</span>
                                </label>
                                <input type="password" id="edit_user_password" placeholder="••••••••" minlength="8"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                            <button type="button" onclick="closeEditUserModal()" class="btn-secondary">Cancel</button>
                            <button type="submit" id="saveEditUserBtn" class="btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end: Edit User Modal-->
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize Yajra DataTable with server-side processing
        $(document).ready(function() {
            const table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.users.index') }}",
                columns: [
                    {
                        data: 'avatar',
                        name: 'avatar',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'email',
                        name: 'email',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
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
                        className: 'text-center',
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

        // Toggle Status via AJAX with SweetAlert Confirmation (Identical to Bobby McDowell Admin)
        function changeStatus(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Change status?',
                text: 'Are you sure you want to toggle the status?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.users.status', ':id') }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Status updated successfully.');
                            $('#users-table').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to update status. Please try again.');
                            $('#users-table').DataTable().ajax.reload(null, false);
                        }
                    });
                } else {
                    // Reset the toggle switch state if cancelled
                    $('#users-table').DataTable().ajax.reload(null, false);
                }
            });
        }

        // Delete User via AJAX with SweetAlert Confirmation (Identical to Bobby McDowell Admin)
        function deleteRecord(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.users.destroy', ':id') }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            toastr.success(res.message || 'User deleted successfully.');
                            $('#users-table').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Deletion failed. The record might be in use.');
                            $('#users-table').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            });
        }

        // Show User Details Modal
        function showUser(button) {
            let user;
            try {
                user = JSON.parse(button.dataset.user || '{}');
            } catch (e) {
                toastr.error('Failed to load user details.');
                return;
            }

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value || 'N/A';
            };

            setText('um_name', user.name || 'User');
            setText('um_email', user.email);
            setText('um_user_id', user.user_id || 'N/A');
            setText('um_plan', user.plan || 'Free');
            setText('um_plan_expiry', user.premium_expires_at || 'N/A');
            setText('um_email_verified', user.email_verified_at || 'No');
            setText('um_gender', user.gender || 'N/A');
            setText('um_background', user.background || 'N/A');
            setText('um_18_plus', user.is_18_plus || 'No');
            setText('um_provider', user.provider || 'Direct');
            setText('um_role', user.role ? (user.role.charAt(0).toUpperCase() + user.role.slice(1)) : 'User');
            setText('um_created_at', user.created_at || 'N/A');

            // Status badge
            const statusEl = document.getElementById('um_status');
            if (statusEl) {
                const status = (user.status || 'active').toLowerCase();
                statusEl.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                if (status === 'active') {
                    statusEl.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
                } else {
                    statusEl.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
                }
            }

            // Avatar
            const avatarImg = document.getElementById('um_avatar');
            const avatarPlaceholder = document.getElementById('um_avatar_placeholder');
            if (user.avatar) {
                avatarImg.src = user.avatar;
                avatarImg.classList.remove('hidden');
                avatarPlaceholder.classList.add('hidden');
            } else {
                avatarImg.removeAttribute('src');
                avatarImg.classList.add('hidden');
                avatarPlaceholder.classList.remove('hidden');
            }

            document.getElementById('userModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        // Close User Details Modal
        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Open Edit User Modal
        function openEditUserModal(button) {
            let user;
            try {
                user = JSON.parse(button.dataset.user || '{}');
            } catch (e) {
                toastr.error('Failed to load user details.');
                return;
            }

            $('#edit_user_id').val(user.id);
            $('#edit_user_name').val(user.name === 'N/A' ? '' : user.name);
            $('#edit_user_email').val(user.email || '');
            $('#edit_user_role').val(user.role || 'User');
            $('#edit_user_status').val((user.status || 'active').toLowerCase());
            $('#edit_user_password').val('');

            document.getElementById('editUserModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        // Close Edit User Modal
        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Submit Edit User Form via AJAX
        function submitEditUser(e) {
            e.preventDefault();
            const userId = $('#edit_user_id').val();
            const btn = $('#saveEditUserBtn');
            btn.prop('disabled', true).html('<i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Saving...');

            $.ajax({
                url: "{{ route('admin.users.update', ':id') }}".replace(':id', userId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    name: $('#edit_user_name').val().trim(),
                    email: $('#edit_user_email').val().trim(),
                    role: $('#edit_user_role').val(),
                    status: $('#edit_user_status').val(),
                    password: $('#edit_user_password').val(),
                },
                success: function(res) {
                    btn.prop('disabled', false).text('Save Changes');
                    if (res.success || res['t-success']) {
                        toastr.success(res.message || 'User updated successfully.');
                        closeEditUserModal();
                        $('#users-table').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(res.message || 'Failed to update user.');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Save Changes');
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                }
            });
        }

        // Close modals on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const userModal = document.getElementById('userModal');
                if (userModal && !userModal.classList.contains('hidden')) {
                    closeUserModal();
                }
                const editModal = document.getElementById('editUserModal');
                if (editModal && !editModal.classList.contains('hidden')) {
                    closeEditUserModal();
                }
            }
        });
    </script>
@endpush

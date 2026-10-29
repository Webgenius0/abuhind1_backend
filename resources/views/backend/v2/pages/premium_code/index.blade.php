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

        <!--begin: Premium Code Details Modal-->
        <div id="premiumCodeModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="pcm_heading">
            <!--begin: Backdrop-->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closePremiumCodeModal()"></div>
            <!--end: Backdrop-->

            <!--begin: Dialog-->
            <div class="relative flex items-center justify-center min-h-screen p-4 pointer-events-none">
                <div class="w-full max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden pointer-events-auto">
                    <!--begin: Header-->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 id="pcm_heading" class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-ticket text-primary-500"></i>
                            <span>Premium Code Details</span>
                        </h3>
                        <button type="button" onclick="closePremiumCodeModal()"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                            title="Close">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <!--end: Header-->

                    <!--begin: Body-->
                    <div class="px-6 py-5 space-y-5 max-h-[75vh] overflow-y-auto">
                        <!-- Promo Code Box with Copy Button -->
                        <div class="p-4 rounded-xl bg-primary-50/50 dark:bg-primary-950/30 border border-primary-200/50 dark:border-primary-800/50 flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400 block mb-0.5">Promo Code</span>
                                <span id="pcm_code" class="font-mono text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100 select-all tracking-wide">-</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="pcm_status_badge" class="px-2.5 py-1 text-xs font-semibold rounded-full">-</span>
                                <button type="button" onclick="copyPromoCode()"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-xs transition-all flex items-center gap-1.5"
                                    title="Copy code">
                                    <i class="fa-regular fa-copy"></i>
                                    <span>Copy</span>
                                </button>
                            </div>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Validity</label>
                                <p id="pcm_validity" class="text-sm font-semibold text-gray-700 dark:text-gray-200">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Redeem Deadline</label>
                                <p id="pcm_expires_at" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Assigned User</label>
                                <p id="pcm_assigned_to" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Used By</label>
                                <p id="pcm_used_by" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Used At</label>
                                <p id="pcm_used_at" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Created By</label>
                                <p id="pcm_created_by" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                            </div>
                        </div>

                        <!-- Note -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Note / Description</label>
                            <p id="pcm_note" class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/80 p-3 rounded-xl border border-gray-100 dark:border-gray-700/60">-</p>
                        </div>

                        <!-- Created Date -->
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs text-gray-400">
                            <span>Created on:</span>
                            <span id="pcm_created_at" class="font-medium text-gray-600 dark:text-gray-300">-</span>
                        </div>
                    </div>
                    <!--end: Body-->

                    <!--begin: Footer-->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <button type="button" onclick="closePremiumCodeModal()" class="btn-secondary">Close</button>
                    </div>
                    <!--end: Footer-->
                </div>
            </div>
            <!--end: Dialog-->
        </div>
        <!--end: Premium Code Details Modal-->
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

        // Show Details Modal Logic
        function showPremiumCode(button) {
            let code;
            try {
                code = JSON.parse(button.dataset.code || '{}');
            } catch (e) {
                toastr.error('Failed to load code details.');
                return;
            }

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value || '—';
            };

            setText('pcm_code', code.code);
            setText('pcm_validity', code.validity);
            setText('pcm_expires_at', code.code_expires_at);
            setText('pcm_assigned_to', code.assigned_user);
            setText('pcm_used_by', code.used_by);
            setText('pcm_used_at', code.used_at);
            setText('pcm_created_by', code.created_by);
            setText('pcm_note', code.note);
            setText('pcm_created_at', code.created_at);

            const badgeEl = document.getElementById('pcm_status_badge');
            if (badgeEl) {
                const st = (code.status || 'unused').toLowerCase();
                badgeEl.textContent = st.charAt(0).toUpperCase() + st.slice(1);
                if (st === 'used') {
                    badgeEl.className = 'px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                } else if (st === 'expired') {
                    badgeEl.className = 'px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-400';
                } else {
                    badgeEl.className = 'px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400';
                }
            }

            document.getElementById('premiumCodeModal').classList.remove('hidden');
        }

        function closePremiumCodeModal() {
            const modal = document.getElementById('premiumCodeModal');
            if (modal) modal.classList.add('hidden');
        }

        function copyPromoCode() {
            const codeEl = document.getElementById('pcm_code');
            const code = codeEl ? codeEl.textContent.trim() : '';
            if (code && code !== '-') {
                navigator.clipboard.writeText(code).then(() => {
                    toastr.success('Promo code copied to clipboard!');
                }).catch(() => {
                    toastr.error('Failed to copy code.');
                });
            }
        }

        // Close on Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closePremiumCodeModal();
            }
        });
    </script>
@endpush

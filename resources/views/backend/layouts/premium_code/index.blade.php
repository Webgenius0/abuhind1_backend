@extends('backend.app')
@section('title', 'Premium Codes')
@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Premium Codes</h5>
                            <a href="{{ route('admin.premium_code.create') }}" class="btn btn-primary">Generate Codes</a>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <select id="status-filter" class="form-select w-auto d-inline-block">
                                    <option value="">All Status</option>
                                    <option value="unused">Unused</option>
                                    <option value="used">Used</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>

                            <table id="premium-code-table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Assigned To</th>
                                        <th>Validity</th>
                                        <th>Status</th>
                                        <th>Used By</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            const table = $('#premium-code-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.premium_code.index') }}",
                    data: function(d) {
                        d.status = $('#status-filter').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'assigned_user',
                        name: 'assigned_user',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'validity',
                        name: 'validity',
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
                        data: 'used_by',
                        name: 'used_by',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#status-filter').on('change', () => table.draw());
        });

        function showDeleteAlert(id) {
            if (!confirm('Delete this code? This cannot be undone.')) return;

            $.ajax({
                url: '/admin/premium-code/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message ?? 'Delete failed.');
                }
            });
        }
    </script>
@endpush

@extends('admin.pages.master')
@section('title', 'Subscriptions')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm"><i class="ri-arrow-left-line"></i> Back</a>
                <h4 class="card-title mb-0">Subscriptions Management</h4>
            </div>
        </div>
        <div class="card-body">
            <table id="subscriptionsTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payments</th>
                        <th>Started At</th>
                        <th>Ends At</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function() {
    var table = $('#subscriptionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('subscriptions.index') }}" + window.location.search,
            data: function(d) {
                d.status = new URLSearchParams(window.location.search).get('status') || '';
                d.client_id = new URLSearchParams(window.location.search).get('client_id') || '';
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'client_name',
                name: 'client_name'
            },
            {
                data: 'amount',
                name: 'amount'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'payments',
                name: 'payments'
            },
            {
                data: 'started_at',
                name: 'started_at'
            },
            {
                data: 'ends_at',
                name: 'ends_at'
            }
        ],
        order: [[5, 'desc']]
    });
});
</script>
@endsection
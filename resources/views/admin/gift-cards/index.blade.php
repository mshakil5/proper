@extends('admin.pages.master')
@section('title', 'Gift Cards')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm"><i class="ri-arrow-left-line"></i> Back</a>
                <h4 class="card-title mb-0">Gift Cards Management</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table id="giftCardsTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Code</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function() {
    var table = $('#giftCardsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('gift-cards.index') }}" + window.location.search,
            data: function(d) {
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
                data: 'code',
                name: 'code'
            },
            {
                data: 'amount',
                name: 'amount'
            },
            {
                data: 'balance',
                name: 'balance'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'created_at',
                name: 'created_at'
            }
        ],
        order: [[6, 'desc']]
    });
});
</script>
@endsection
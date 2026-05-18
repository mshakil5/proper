@extends('admin.pages.master')
@section('title', 'Orders')

@section('content')
<div class="container-fluid mb-3">

    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        
                        <div class="btn-group flex-wrap">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-{{ request('status') == null ? 'primary' : 'light' }}">All Orders</a>
                            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-{{ request('status') == 'pending' ? 'warning' : 'light' }}">Pending</a>
                            <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="btn btn-{{ request('status') == 'confirmed' ? 'info' : 'light' }}">Confirmed</a>
                            <a href="{{ route('admin.orders.index', ['status' => 'preparing']) }}" class="btn btn-{{ request('status') == 'preparing' ? 'primary' : 'light' }}">Preparing</a>
                            <a href="{{ route('admin.orders.index', ['status' => 'ready']) }}" class="btn btn-{{ request('status') == 'ready' ? 'info' : 'light' }}">Ready</a>
                            <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="btn btn-{{ request('status') == 'delivered' ? 'success' : 'light' }}">Delivered</a>
                            <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="btn btn-{{ request('status') == 'cancelled' ? 'danger' : 'light' }}">Cancelled</a>
                        </div>

                        <div>
                            @if(request('type') === 'pos')
                                <span class="badge bg-warning fs-6">POS Orders</span>
                            @elseif(request('type') === 'frontend')
                                <span class="badge bg-primary fs-6">Online Orders</span>
                            @else
                                <span class="badge bg-secondary fs-6">All Orders</span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
            <h5 class="mb-0 d-flex justify-content-between align-items-center">
                <span>🔎 Advanced Filters</span>
                <i class="ri-arrow-down-s-line"></i>
            </h5>
        </div>
        <div class="collapse" id="advancedFilters">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 d-none">
                        <label class="form-label">Sale Type</label>
                        <select id="orderTypeFilter" class="form-select">
                            <option value="all">All (POS + Online)</option>
                            <option value="pos">POS Sale</option>
                            <option value="frontend">Online Sale</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Payment Method</label>
                        <select id="paymentMethodFilter" class="form-select">
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="stripe">Stripe</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">From Date</label>
                        <input type="date" id="startDateFilter" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">To Date</label>
                        <input type="date" id="endDateFilter" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Customer Name / Email / Phone</label>
                        <input type="text" id="customerFilter" class="form-control" placeholder="Search customer...">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Order Number (#ORD-...)</label>
                        <input type="text" id="orderNumberFilter" class="form-control" placeholder="e.g. ORD-1775988411-7905">
                    </div>
                </div>

                <div class="mt-3">
                    <button id="applyFilters" class="btn btn-primary me-2">Apply Filters</button>
                    <button id="resetFilters" class="btn btn-secondary">Reset All</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
            <table id="ordersTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Order Status</th>
                        <th>Payment Method</th>
                        <th>Payment Status</th>
                        <th>Date</th>
                        <th>Action</th>
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
    var table = $('#ordersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.orders.index') }}",
            data: function(d) {
                d.status          = new URLSearchParams(window.location.search).get('status') || '';
                d.client_id      = new URLSearchParams(window.location.search).get('client_id') || '';
                d.order_type      = $('#orderTypeFilter').val();
                d.payment_method  = $('#paymentMethodFilter').val();
                d.customer        = $('#customerFilter').val();
                d.start_date      = $('#startDateFilter').val();
                d.end_date        = $('#endDateFilter').val();
                d.type = new URLSearchParams(window.location.search).get('type') || '';

                if ($('#orderNumberFilter').val().trim() !== '') {
                    d.order_number = $('#orderNumberFilter').val().trim();
                }
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'order_number', name: 'order_number' },
            { data: 'customer', name: 'customer' },
            { data: 'amounts', name: 'total' },
            { data: 'delivery_type', name: 'delivery_type' },
            { data: 'order_status', name: 'status' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'date', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[9, 'desc']]
    });

    $('#applyFilters').on('click', function() {
        table.ajax.reload();
    });

    $('#resetFilters').on('click', function() {
        $('#orderTypeFilter').val('all');
        $('#paymentMethodFilter').val('');
        $('#customerFilter').val('');
        $('#orderNumberFilter').val('');
        $('#startDateFilter').val('');
        $('#endDateFilter').val('');
        table.ajax.reload();
    });
});
</script>
@endsection
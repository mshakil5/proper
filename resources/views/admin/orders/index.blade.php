@extends('admin.pages.master')
@section('title', 'Orders')

@section('content')
<div class="container-fluid mb-3">
    <div class="card">
        <div class="card-body py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-{{ request('status') == null ? 'primary' : 'light' }}">
                            All
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-{{ request('status') == 'pending' ? 'warning' : 'light' }}">
                            Pending
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="btn btn-{{ request('status') == 'confirmed' ? 'info' : 'light' }}">
                            Confirmed
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'preparing']) }}" class="btn btn-{{ request('status') == 'preparing' ? 'primary' : 'light' }}">
                            Preparing
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'ready']) }}" class="btn btn-{{ request('status') == 'ready' ? 'info' : 'light' }}">
                            Ready
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="btn btn-{{ request('status') == 'delivered' ? 'success' : 'light' }}">
                            Delivered
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="btn btn-{{ request('status') == 'cancelled' ? 'danger' : 'light' }}">
                            Cancelled
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" id="customerFilter" class="form-control" placeholder="Name, email or phone...">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="startDateFilter" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="endDateFilter" class="form-control">
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="applyFilters" class="btn btn-primary w-100" title="Filter">
                                <i class="ri-filter-3-line"></i>
                            </button>
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="resetFilters" class="btn btn-secondary w-100" title="Reset">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                @if(request('client_id'))
                    <a href="{{ url()->previous() }}" class="btn btn-light btn-sm"><i class="ri-arrow-left-line"></i> Back</a>
                @endif
                <h4 class="card-title mb-0">
                    Order Management
                    @if(request('client_id'))
                        @php $client = \App\Models\User::find(request('client_id')); @endphp
                        @if($client)
                            <span class="text-muted fs-6 fw-normal ms-1">— {{ $client->first_name }} {{ $client->last_name }}</span>
                        @endif
                    @endif
                </h4>
            </div>
        </div>
        <div class="card-body">
            <table id="ordersTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Order Status</th>
                        <th>Payment Type</th>
                        <th>Payment Status</th>
                        <th>Date</th>
                        <th>Action</th>
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
    var table = $('#ordersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.orders.index') }}" + window.location.search,
            data: function(d) {
                d.status = new URLSearchParams(window.location.search).get('status') || '';
                d.customer = $('#customerFilter').val();
                d.start_date = $('#startDateFilter').val();
                d.end_date = $('#endDateFilter').val();
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
                data: 'order_number',
                name: 'order_number'
            },
            {
                data: 'customer',
                name: 'customer'
            },
            {
                data: 'amounts',
                name: 'total'
            },
            {
                data: 'delivery_type',
                name: 'delivery_type'
            },
            {
                data: 'order_status',
                name: 'status'
            },
            { data: 'payment_method', name: 'payment_method' },
            {
                data: 'payment_status',
                name: 'payment_status'
            },
            {
                data: 'date',
                name: 'created_at'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        order: [[6, 'desc']]
    });

    $('#applyFilters').on('click', function() {
        table.ajax.reload();
    });

    $('#resetFilters').on('click', function() {
        $('#customerFilter').val('');
        $('#startDateFilter').val('');
        $('#endDateFilter').val('');
        window.location.href = "{{ route('admin.orders.index') }}";
    });
});
</script>
@endsection
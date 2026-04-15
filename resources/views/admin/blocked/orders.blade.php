@extends('admin.pages.master')
@section('title', 'Blocked Customer Orders')

@section('content')

<div class="container-fluid mb-3">
    @if($blockedCustomer)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Filtered by:</strong> {{ $blockedCustomer->email }}
            <a href="{{ route('admin.blocked-orders.index') }}" class="btn btn-sm btn-light ms-2">View All Orders</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

<div class="container-fluid">
    <div class="card">

        <div class="card-header">
            <h4 class="card-title mb-0">Blocked Customer Orders</h4>
        </div>

        <div class="card-body">
            <table id="blockedOrdersTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Blocked As</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</div>

<div class="modal fade" id="viewOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderModalBody">
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let url = "{{ route('admin.blocked-orders.index') }}";
        @if($blockedCustomerId)
            url += "?blocked_customer_id={{ $blockedCustomerId }}";
        @endif

        let table = $('#blockedOrdersTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: url,
            columns: [
                {
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'blocked_customer',
                    name: 'blocked_customer'
                },
                {
                    data: 'total_items',
                    name: 'total_items',
                    orderable: false
                },
                { data: 'total', name: 'total', orderable: false },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [0, 'desc']
            ]
        });

        $(document).on('click', '.viewBtn', function () {
            let id = $(this).data('id');

            $.get('/admin/blocked-orders/' + id, function (data) {

                let cart = data.cart || [];
                let summary = data.summary || {};
                let customer = data.customer || {};
                let delivery = data.delivery || {};

                let itemsHtml = '';

                cart.forEach((item, i) => {
                    itemsHtml += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${item.name ?? '-'}</td>
                            <td>${item.quantity ?? 0}</td>
                        </tr>
                    `;
                });

                let html = `
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Email:</strong> ${data.email}
                        </div>
                        <div class="col-6">
                            <strong>Phone:</strong> ${data.phone}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Date:</strong> ${data.created_at}
                        </div>
                    </div>

                    <h6>Items</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml || '<tr><td colspan="3" class="text-center">No items</td></tr>'}
                        </tbody>
                    </table>

                    <div class="text-end mt-3">
                        <h5>Total: £${summary.total ?? 0}</h5>
                    </div>
                `;

                $('#orderModalBody').html(html);
                new bootstrap.Modal('#viewOrderModal').show();
            });
        });
    });
</script>
@endsection
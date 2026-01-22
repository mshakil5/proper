@extends('admin.pages.master')
@section('title', 'Order Details - #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Order Details - #{{ $order->order_number }}</h4>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i> Back to Orders
                </a>
            </div>
        </div>
        <div class="card-body">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <div class="border p-3 rounded">
                        <p><strong>Name:</strong> {{ $order->first_name }} {{ $order->last_name }}</p>
                        <p><strong>Email:</strong> {{ $order->email }}</p>
                        <p><strong>Phone:</strong> {{ $order->phone }}</p>
                        @if($order->user)
                        <p><strong>Account Type:</strong> Registered</p>
                        @else
                        <p><strong>Account Type:</strong> Guest</p>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5>Delivery Information</h5>
                    <div class="border p-3 rounded">
                        <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $order->delivery_type)) }}</p>
                        <p><strong>Time:</strong> {{ $order->time }}</p>
                        @if($order->delivery_type === 'delivery')
                            <p><strong>Address:</strong> {{ $order->street }}, {{ $order->city }}, {{ $order->postcode }}</p>
                            @if($order->address_2)
                            <p><strong>Apt/Suite:</strong> {{ $order->address_2 }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <h5>Order Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Options</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>
                                @if($item->options->count() > 0)
                                    <ul style="margin: 0; padding-left: 20px;">
                                        @foreach($item->options as $option)
                                        <li style="font-size: 12px;">{{ $option->option_name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>£{{ number_format($item->price, 2) }}</td>
                            <td>£{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 offset-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td><strong>Subtotal:</strong></td>
                                    <td class="text-end">£{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Delivery Charge:</strong></td>
                                    <td class="text-end">£{{ number_format($order->delivery_charge, 2) }}</td>
                                </tr>
                                @if($order->coupon_discount > 0)
                                <tr>
                                    <td><strong>Coupon Discount:</strong></td>
                                    <td class="text-end">-£{{ number_format($order->coupon_discount, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->gift_card_discount > 0)
                                <tr>
                                    <td><strong>Gift Card Discount:</strong></td>
                                    <td class="text-end">-£{{ number_format($order->gift_card_discount, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->points_used > 0)
                                <tr>
                                    <td><strong>Points Used:</strong></td>
                                    <td class="text-end">-£{{ number_format($order->points_used, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="table-active">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong>£{{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Order Status</h5>
                    <div class="border p-3 rounded">
                        <p>
                            <strong>Status:</strong> 
                            <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : ($order->status == 'cancelled' ? 'danger' : 'info')) }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </p>
                        <p><strong>HubRise Order ID:</strong> {{ $order->hubrise_order_id ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5>Payment Information</h5>
                    <div class="border p-3 rounded">
                        <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        <p>
                            <strong>Status:</strong> 
                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                        @if($order->payment_transaction_id)
                        <p><strong>Transaction ID:</strong> {{ $order->payment_transaction_id }}</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($order->notes)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Order Notes</h5>
                    <div class="border p-3 rounded">
                        <p>{{ $order->notes }}</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-12">
                    <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
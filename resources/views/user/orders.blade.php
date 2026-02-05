<!-- resources/views/user/orders.blade.php -->

@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-shopping-bag"></i>
        Your Orders
    </h3>
    <p class="dashboard-subtitle">View and manage all your orders</p>

    <div class="orders-table-wrapper">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse(auth()->user()->orders()->latest()->paginate(10) as $order)
                    <tr>
                        <td><span class="order-id">#{{ $order->order_number }}</span></td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="order-status 
                                @if($order->status === 'delivered') status-delivered 
                                @elseif($order->status === 'pending') status-pending 
                                @elseif($order->status === 'confirmed') status-confirmed 
                                @elseif($order->status === 'preparing') status-preparing 
                                @elseif($order->status === 'ready') status-ready 
                                @elseif($order->status === 'cancelled') status-cancelled 
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="order-status 
                                @if($order->payment_status === 'paid') status-paid
                                @elseif($order->payment_status === 'pending') status-pending
                                @elseif($order->payment_status === 'failed') status-failed
                                @elseif($order->payment_status === 'refunded') status-refunded
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td><span class="order-amount">£{{ number_format($order->total, 2) }}</span></td>
                        <td class="order-actions">
                            <a href="{{ route('user.orders.details', $order->id) }}" class="btn-view-order">View</a>
                            <button class="btn-view-order btn-order-again" data-order='@json($order)'>Order Again</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No orders available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ auth()->user()->orders()->latest()->paginate(10)->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@endsection
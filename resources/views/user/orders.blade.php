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
                    <th>Items</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="order-id">#ORD123456</span></td>
                    <td>Jan 15, 2026</td>
                    <td>3 items</td>
                    <td><span class="order-status status-delivered">Delivered</span></td>
                    <td><span class="order-amount">$45.50</span></td>
                    <td class="order-actions">
                        <a href="#" class="btn-view-order">View</a>
                        <a href="#" class="btn-reorder">Reorder</a>
                    </td>
                </tr>
                <tr>
                    <td><span class="order-id">#ORD123455</span></td>
                    <td>Jan 12, 2026</td>
                    <td>2 items</td>
                    <td><span class="order-status status-pending">In Transit</span></td>
                    <td><span class="order-amount">$32.00</span></td>
                    <td class="order-actions">
                        <a href="#" class="btn-view-order">View</a>
                    </td>
                </tr>
                <tr>
                    <td><span class="order-id">#ORD123454</span></td>
                    <td>Jan 10, 2026</td>
                    <td>4 items</td>
                    <td><span class="order-status status-delivered">Delivered</span></td>
                    <td><span class="order-amount">$28.75</span></td>
                    <td class="order-actions">
                        <a href="#" class="btn-view-order">View</a>
                        <a href="#" class="btn-reorder">Reorder</a>
                    </td>
                </tr>
                <tr>
                    <td><span class="order-id">#ORD123453</span></td>
                    <td>Jan 8, 2026</td>
                    <td>2 items</td>
                    <td><span class="order-status status-delivered">Delivered</span></td>
                    <td><span class="order-amount">$56.25</span></td>
                    <td class="order-actions">
                        <a href="#" class="btn-view-order">View</a>
                        <a href="#" class="btn-reorder">Reorder</a>
                    </td>
                </tr>
                <tr>
                    <td><span class="order-id">#ORD123452</span></td>
                    <td>Jan 5, 2026</td>
                    <td>5 items</td>
                    <td><span class="order-status status-cancelled">Cancelled</span></td>
                    <td><span class="order-amount">$67.80</span></td>
                    <td class="order-actions">
                        <a href="#" class="btn-view-order">View</a>
                        <a href="#" class="btn-reorder">Reorder</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
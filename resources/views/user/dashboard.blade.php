@extends('user.master')

@section('user-content')

<div class="welcome-section">
    <div class="welcome-content">
        <h2>Welcome back, {{ auth()->user()->name }}! 👋</h2>
        <p>Manage your account, orders, and rewards all in one place</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-box-icon"><i class="fas fa-shopping-bag"></i></div>
        <div class="stat-box-value">12</div>
        <div class="stat-box-label">Total Orders</div>
    </div>
    <div class="stat-box">
        <div class="stat-box-icon"><i class="fas fa-star"></i></div>
        <div class="stat-box-value">450</div>
        <div class="stat-box-label">Reward Points</div>
    </div>
    <div class="stat-box">
        <div class="stat-box-icon"><i class="fas fa-ticket-alt"></i></div>
        <div class="stat-box-value">3</div>
        <div class="stat-box-label">Active Coupons</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="#" class="quick-action-btn" data-tab="profile">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="#" class="quick-action-btn" data-tab="orders">
        <i class="fas fa-shopping-bag"></i>
        <span>Orders</span>
    </a>
    <a href="#" class="quick-action-btn" data-tab="coupons">
        <i class="fas fa-ticket-alt"></i>
        <span>Coupons</span>
    </a>
    <a href="#" class="quick-action-btn" data-tab="points">
        <i class="fas fa-star"></i>
        <span>Points</span>
    </a>
</div>

<!-- Recent Orders -->
<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-clock"></i>
        Recent Orders
    </h3>
    <div class="orders-table-wrapper">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="order-id">#ORD123456</span></td>
                    <td>Jan 15, 2026</td>
                    <td><span class="order-status status-delivered">Delivered</span></td>
                    <td><span class="order-amount">$45.50</span></td>
                    <td class="order-actions">
                        <button class="btn-view-order">View</button>
                    </td>
                </tr>
                <tr>
                    <td><span class="order-id">#ORD123455</span></td>
                    <td>Jan 12, 2026</td>
                    <td><span class="order-status status-pending">In Transit</span></td>
                    <td><span class="order-amount">$32.00</span></td>
                    <td class="order-actions">
                        <button class="btn-view-order">View</button>
                    </td>
                </tr>
                <tr>
                    <td><span class="order-id">#ORD123454</span></td>
                    <td>Jan 10, 2026</td>
                    <td><span class="order-status status-delivered">Delivered</span></td>
                    <td><span class="order-amount">$28.75</span></td>
                    <td class="order-actions">
                        <button class="btn-view-order">View</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
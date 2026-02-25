@extends('user.master')

@section('user-content')

<div class="welcome-section">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
        <div class="welcome-content">
            <h2>Welcome back, {{ trim(auth()->user()->first_name.' '.auth()->user()->last_name) }}! 👋</h2>
            <p>Manage your account, orders, and rewards all in one place</p>
        </div>
        <div style="background:rgba(255,255,255,0.15);border-radius:12px;padding:16px 20px;text-align:center;flex-shrink:0;">
            <p style="margin:0 0 4px 0;font-size:13px;color:rgba(255,255,255,0.85);">Earn <strong>+100 Points</strong> for a Google review</p>
                <a href="https://maps.app.goo.gl/qeahZVMrucMFAPDZ9" target="_blank" style="display:inline-flex;align-items:center;gap:7px;background:#fff;color:#ff5a00;font-size:13px;font-weight:700;padding:8px 18px;border-radius:30px;text-decoration:none;margin-top:6px;position:relative;z-index:1;">
                    <i class="fab fa-google"></i> Review Now
                </a>
        </div>
    </div>
</div>

@include('user.subscription-message')

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-box-icon"><i class="fas fa-shopping-bag"></i></div>
        <div class="stat-box-value">{{ auth()->user()->orders()->count() }}</div>
        <div class="stat-box-label">Total Orders</div>
    </div>
    <div class="stat-box">
        <div class="stat-box-icon"><i class="fas fa-star"></i></div>
        <div class="stat-box-value">{{ auth()->user()->available_points }}</div>
        <div class="stat-box-label">Reward Points</div>
    </div>
    <div class="stat-box">
        <div class="stat-box-icon"><i class="fas fa-ticket-alt"></i></div>
        <div class="stat-box-value">{{ \App\Models\Coupon::where('is_birthday_voucher', false)->where('is_active', true)->count() + \App\Models\Coupon::where('is_birthday_voucher', true)->where('is_active', true)->count() }}</div>
        <div class="stat-box-label">Active Coupons</div>
    </div>
    <div class="stat-box">
        <div class="stat-box-icon"><i class="fas fa-gift"></i></div>
        <div class="stat-box-value">{{ auth()->user()->purchasedGiftCards()->where('balance','>',0)->count(); }}</div>
        <div class="stat-box-label">Active Gift Cards</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="{{ route('user.profile') }}" class="quick-action-btn" data-tab="profile">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="{{ route('user.orders') }}" class="quick-action-btn" data-tab="orders">
        <i class="fas fa-shopping-bag"></i>
        <span>Orders</span>
    </a>
    <a href="{{ route('user.coupons') }}" class="quick-action-btn" data-tab="coupons">
        <i class="fas fa-ticket-alt"></i>
        <span>Coupons</span>
    </a>
    <a href="{{ route('user.points') }}" class="quick-action-btn" data-tab="points">
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
                    <th>Order Status</th>
                    <th>Payment Status</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse(auth()->user()->orders()->latest()->take(10)->get() as $order)
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
                            <span class="badge bg-{{ $order->payment?->status_badge ?? 'secondary' }}">
                                {{ ucfirst($order->payment?->status ?? 'pending') }}
                            </span>
                        </td>
                        <td><span class="order-amount">£{{ number_format($order->total, 2) }}</span></td>
                        <td class="order-actions">
                            <a href="{{ route('user.orders.details', $order->id) }}" class="btn-view-order">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No orders available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
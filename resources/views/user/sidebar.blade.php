<div class="user-sidebar">
    <div class="user-sidebar-header">
        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <h6>{{ trim(auth()->user()->first_name.' '.auth()->user()->last_name) }}</h6>
            <p>{{ auth()->user()->email }}</p>
        </div>
    </div>

    <nav class="user-nav">
        <a href="{{ route('dashboard') }}" class="user-nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" data-tab="dashboard">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('user.profile') }}" class="user-nav-item {{ request()->routeIs('user.profile') ? 'active' : '' }}" data-tab="profile">
            <i class="fas fa-user"></i>
            <span>Profile Update</span>
        </a>
        
        <a href="{{ route('user.password') }}" class="user-nav-item {{ request()->routeIs('user.password') ? 'active' : '' }}" data-tab="password">
            <i class="fas fa-lock"></i>
            <span>Change Password</span>
        </a>
        
        <a href="{{ route('user.orders') }}" class="user-nav-item {{ request()->routeIs('user.orders') ? 'active' : '' }}" data-tab="orders">
            <i class="fas fa-shopping-bag"></i>
            <span>Orders</span>
        </a>
        
        <a href="{{ route('user.coupons') }}" class="user-nav-item {{ request()->routeIs('user.coupons') ? 'active' : '' }}" data-tab="coupons">
            <i class="fas fa-ticket-alt"></i>
            <span>Coupons</span>
        </a>
        
        <a href="{{ route('user.points') }}" class="user-nav-item {{ request()->routeIs('user.points') ? 'active' : '' }}" data-tab="points">
            <i class="fas fa-star"></i>
            <span>Points History</span>
        </a>
        
        <a href="{{ route('user.social') }}" class="user-nav-item {{ request()->routeIs('user.social') ? 'active' : '' }}" data-tab="social">
            <i class="fas fa-share-alt"></i>
            <span>Social Sharing</span>
        </a>

        <div class="user-nav-divider"></div>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="user-nav-item user-nav-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </nav>
</div>
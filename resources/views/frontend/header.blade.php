<nav class="navbar navbar-dark shadow-sm navbar-expand-lg py-3 sticky-top fw-bold bg-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            @if(isset($company->company_logo) && $company->company_logo != '')
                <img 
                    id="company_logo_preview"
                    src="{{ asset('uploads/company/' . $company->company_logo) }}"
                    alt="Company Logo"
                    class="me-2"
                    style="width:180px; height:40px; object-fit:contain;"
                >
            @endif

            <span class="fw-bold fs-5 d-none">{{ $company->company_name ?? '' }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('menu') ? 'active' : '' }}" href="{{ route('menu') }}">Menu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('gift-cards') ? 'active' : '' }}" href="{{ route('gift-cards') }}">Gift Cards</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('our-story') ? 'active' : '' }}" href="{{ route('our-story') }}">Our Story</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('find-us') ? 'active' : '' }}" href="{{ route('find-us') }}">Find Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
            </li>
            <li class="nav-item">
                @auth
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                @else
                    <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                        Login
                    </a>
                @endauth
            </li>
            </ul>
            <a href="https://www.propertakeaways.com/menu" class="btn btn-gradient ms-3 fw-semibold" target="_blank">
                <i class="fa-solid fa-bag-shopping me-1"></i> Order Now
            </a>
        </div>
    </div>
</nav>

<div class="marquee-bar">
    <div class="marquee-wrapper overflow-hidden position-relative">
        <div class="marquee-content">
            <span>Fast Delivery •</span>
            <span>Authentic Taste •</span>
            <span>Fresh Food •</span>
            <span>Fast Delivery •</span>
            <span>Authentic Taste •</span>
            <span>Fresh Food •</span>
            <span>Fast Delivery •</span>
            <span>Authentic Taste •</span>
            <span>Fresh Food •</span>
            <span>Fast Delivery •</span>
            <span>Authentic Taste •</span>
            <span>Fresh Food •</span>
            <span>Fast Delivery •</span>
            <span>Authentic Taste •</span>
            <span>Fresh Food •</span>
            <span>Fast Delivery •</span>
            <span>Authentic Taste •</span>
            <span>Fresh Food •</span>
        </div>
    </div>
</div>

<div class="floating-shop-status swing @if (request()->routeIs('checkout')) d-none @endif" id="shopStatus">
    <div class="status-text">OPEN</div>
    <a href="{{ route('menu') }}" class="order-btn-inside" id="orderBtnInside" target="_blank">
        <i class="fa-solid fa-bag-shopping me-1"></i> Order
    </a>
</div>

<style>
.floating-shop-status {
    position: fixed;
    top: 200px;
    right: 20px;
    z-index: 998;
    background: #fff;
    border: 2px solid;
    border-radius: 5px;
    padding: 10px 20px;
    font-weight: bold;
    font-size: 18px;
    box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
    transform-origin: top center;
    text-align: center;
    pointer-events: auto;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.status-text {
    pointer-events: none;
}

.order-btn-inside {
    display: none;
    background: linear-gradient(90deg, #ff8a00, #ff5a00) !important;
    color: #fff !important;
    padding: 8px 10px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 13px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.order-btn-inside:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    color: #fff !important;
}

.order-btn-inside.show {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 576px) {
    .floating-shop-status {
        top: 170px;
        right: 35px;
        transform: translateX(50%);
        padding: 8px 8px;
        font-size: 16px;
    }

    .order-btn-inside {
        padding: 6px 14px;
        font-size: 12px;
    }
}

@media (min-width: 577px) and (max-width: 768px) {
    .floating-shop-status {
        top: 170px;
        right: 85px;
        transform: translateX(50%);
        padding: 10px 25px;
        font-size: 17px;
    }
}

.floating-shop-status::before {
    content: "";
    position: absolute;
    top: -20px;
    left: 50%;
    width: 2px;
    height: 20px;
    background: #555;
    transform: translateX(-50%);
    z-index: 1;
}

.floating-shop-status::after {
    content: "";
    position: absolute;
    top: -40px;
    left: 50%;
    width: 30px;
    height: 30px;
    border: 2px solid #555;
    border-radius: 50%;
    background: #fff;
    transform: translateX(-50%);
    z-index: 0;
}

.floating-shop-status.open {
    color: green;
    border-color: green;
}

.floating-shop-status.closed {
    color: red;
    border-color: red;
}

.swing {
    animation: swingBoard 3s ease-in-out infinite;
}

@keyframes swingBoard {
    0% { transform: rotate(2deg); }
    50% { transform: rotate(-2deg); }
    100% { transform: rotate(2deg); }
}
</style>

<script>
    const ShopStatus = {
        isOpen() {
            // return true;
            const now = new Date(
                new Date().toLocaleString('en-GB', { timeZone: 'Europe/London' })
            );
            const day = now.getDay();
            const hour = now.getHours();
            const minute = now.getMinutes();
            const currentMinutes = hour * 60 + minute;

            if (day === 0) {
                const openTime = 16 * 60 + 30;
                const closeTime = 22 * 60;
                return currentMinutes >= openTime && currentMinutes < closeTime;
            } else if (day >= 1 && day <= 6) {
                const openTime = 16 * 60 + 30;
                const closeTime = 23 * 60 + 30;
                return currentMinutes >= openTime && currentMinutes < closeTime;
            }
            return false;
        },

        getStatus() {
            return this.isOpen() ? 'OPEN' : 'CLOSED';
        },

        updateDisplay() {
            const element = document.getElementById('shopStatus');
            const orderBtn = document.getElementById('orderBtnInside');
            const cartBtn = document.getElementById('cartFloatBtn');
            
            if (element) {
                element.querySelector('.status-text').textContent = this.getStatus();
                element.classList.remove('open', 'closed');
                element.classList.add(this.isOpen() ? 'open' : 'closed');
            }

            if (orderBtn) {
                if (this.isOpen()) {
                    orderBtn.classList.add('show');
                } else {
                    orderBtn.classList.remove('show');
                }
            }

            if (cartBtn) {
                if (this.isOpen()) {
                    cartBtn.classList.remove('d-none');
                } else {
                    cartBtn.classList.add('d-none');
                }
            }
        }
    };

    window.ShopStatus = ShopStatus;

    document.addEventListener('DOMContentLoaded', () => {
        const update = () => {
            ShopStatus.updateDisplay();
            if (!ShopStatus.isOpen())
                document.querySelectorAll('.open-product').forEach(b => b.remove());
        };
        update();
        setInterval(update, 60000);
    });
</script>
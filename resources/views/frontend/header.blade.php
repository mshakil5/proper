<nav class="navbar navbar-dark shadow-sm navbar-expand-lg py-3 sticky-top fw-bold bg-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            @if(isset($company->company_logo) && $company->company_logo != '')
                <img 
                    id="company_logo_preview"
                    src="{{ asset('uploads/company/' . $company->company_logo) }}"
                    alt="{{ $company->company_name ?? '' }}"
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
            <a href="https://www.propertakeaways.co.uk/menu" class="btn btn-gradient ms-3 fw-semibold" target="_blank">
                <i class="fa-solid fa-bag-shopping me-1"></i> Order Now
            </a>
        </div>
    </div>
</nav>

<div class="marquee-bar d-none">
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
    <a href="{{ route('menu') }}" class="order-btn-inside" id="orderBtnInside">
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

    const SHOP_HOURS = {
        Monday:    { open: '16:00', close: '22:30' },
        Tuesday:   { open: '16:00', close: '22:30' },
        Wednesday: { open: '16:00', close: '22:30' },
        Thursday:  { open: '16:00', close: '22:30' },
        Friday:    { open: '16:00', close: '22:30' },
        Saturday:  { open: '16:00', close: '22:30' },
        Sunday:    { open: '16:00', close: '22:30' },
    };

    const ShopStatus = {
        getUKTime() {
            const parts = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Europe/London',
                weekday: 'long',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).formatToParts(new Date());

            return {
                day: parts.find(p => p.type === 'weekday').value,
                hour: +parts.find(p => p.type === 'hour').value,
                minute: +parts.find(p => p.type === 'minute').value
            };
        },

        toMinutes(timeStr) {
            const [h, m] = timeStr.split(':').map(Number);
            return h * 60 + m;
        },

        getState() {
            const { day, hour, minute } = this.getUKTime();
            const nowMin = hour * 60 + minute;

            const openAt  = this.toMinutes(SHOP_HOURS[day].open);
            const closeAt = this.toMinutes(SHOP_HOURS[day].close);

            const isOpen = nowMin >= openAt && nowMin < closeAt;

            return { isOpen, day, hour, minute };
        },

        updateDisplay() {
            const state = this.getState();

            const element  = document.getElementById('shopStatus');
            const orderBtns = document.querySelectorAll('.addToOrderBtn');
            const cartBtn  = document.getElementById('cartFloatBtn');

            if (element) {
                element.querySelector('.status-text').textContent =
                    state.isOpen ? 'OPEN' : 'OPEN AT 4:00PM';

                element.classList.toggle('open', state.isOpen);
                element.classList.toggle('closed', !state.isOpen);
            }

            /*
            if (orderBtns.length > 0) {
                orderBtns.forEach(btn => {
                    btn.classList.toggle('d-none', !state.isOpen);
                    btn.style.pointerEvents = state.isOpen ? 'auto' : 'none';
                });
            }

            if (cartBtn) {
                cartBtn.classList.toggle('d-none', !state.isOpen);
                cartBtn.style.pointerEvents = state.isOpen ? 'auto' : 'none';
            }
            */
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        ShopStatus.updateDisplay();
        setInterval(() => ShopStatus.updateDisplay(), 60000);
    });
</script>
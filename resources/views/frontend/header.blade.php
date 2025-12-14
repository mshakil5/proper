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
                <a class="nav-link {{ request()->routeIs('our-story') ? 'active' : '' }}" href="{{ route('our-story') }}">Our Story</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('find-us') ? 'active' : '' }}" href="{{ route('find-us') }}">Find Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
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

<div class="floating-shop-status swing" id="shopStatus">
    OPEN
</div>

<style>
.floating-shop-status {
    position: fixed;
    top: 200px;
    right: 20px;
    z-index: 9999;
    background: #fff;
    border: 2px solid;
    border-radius: 5px;
    padding: 10px 25px;
    font-weight: bold;
    font-size: 18px;
    box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
    transform-origin: top center;
    text-align: center;
}

@media (max-width: 768px) {
    .floating-shop-status {
        top: 60px;
        padding: 8px 20px;
        font-size: 16px;
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
    const shopStatus = document.getElementById('shopStatus');

    function updateShopStatus() {
        const now = new Date();
        const day = now.getDay();
        const hour = now.getHours();
        const minute = now.getMinutes();
        let open = false;

        const currentMinutes = hour * 60 + minute;

        if (day === 0) {
            const openTime = 16 * 60 + 30;
            const closeTime = 22 * 60;
            if (currentMinutes >= openTime && currentMinutes < closeTime) {
                open = true;
            }
        } else if (day >= 1 && day <= 6) {
            const openTime = 16 * 60 + 30;
            const closeTime = 23 * 60 + 30;
            if (currentMinutes >= openTime && currentMinutes < closeTime) {
                open = true;
            }
        }

        if(open) {
            shopStatus.classList.add('open');
            shopStatus.classList.remove('closed');
            shopStatus.textContent = "OPEN";
        } else {
            shopStatus.classList.add('closed');
            shopStatus.classList.remove('open');
            shopStatus.textContent = "CLOSED";
        }
    }

    updateShopStatus();

    setInterval(updateShopStatus, 60000);
</script>
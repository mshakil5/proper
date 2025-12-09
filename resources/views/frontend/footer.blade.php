    <section class="subscribe-section d-none">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3>Stay in the <span style="color:var(--orange)">Loop</span></h3>
                    <p>Subscribe for exclusive offers, new menu alerts, and tasty updates. No spam, just flavour.</p>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <form class="d-flex justify-content-lg-end align-items-center subscribe-box" id="subscribeForm"
                        onsubmit="return false;">
                        <input class="subscribe-input me-2" id="subscriberEmail" placeholder="Enter your email"
                            type="email" required>
                        <button class="subscribe-btn" id="subscribeBtn"><i class="fa-solid fa-paper-plane"></i>
                            Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section style="background: #f5f5dc;">
        <div class="container">
            <div class="row text-dark p-4">

                <!-- Delivery Information -->
                <div class="col-md-6 p-4 mb-4" style="background-color: rgb(232 232 200);">
                    <h6>Minimum Order For Delivery</h6>
                    <table class="table table-bordered table-sm text-dark mb-2">
                        <tbody>
                            <tr>
                                <td>Minimum Order £12.00</td>
                                <td>Within 4.5 Miles</td>
                            </tr>
                            <tr>
                                <td>Minimum Order £20.00</td>
                                <td>Within 7.5 Miles</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="small text-muted">
                        * Note: You must spend at least this amount on the items, after discount, excluding any delivery
                        or processing fees.
                    </p>
                </div>

                <div class="col-md-2"></div>

                <!-- Opening Hours -->
                <div class="col-md-4 p-4" style="background-color: rgb(232 232 200);">
                    <h6>Opening Hours</h6>
                    <table class="table table-bordered table-sm text-dark mb-2">
                        <tbody>
                            <tr>
                                <td>Mon - Sat</td>
                                <td>16:00 - 22:00</td>
                            </tr>
                            <tr>
                                <td>Sunday</td>
                                <td>16:00 - 22:00</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="small text-muted">
                        Further Information: Our online ordering website will stop taking orders ten minutes before we close.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-3">
                        <div>
                            @if (isset($company->company_logo) && $company->company_logo != '')
                                <img id="footer_logo_preview"
                                    src="{{ asset('images/company/' . $company->company_logo) }}" alt="Footer Logo"
                                    style="width:260px; height:60px; object-fit:contain;">
                            @endif
                            <p style="margin-top:8px;color:#c0c0c0">{{ $company->footer_content ?? '' }}</p>
                            <div class="mt-2">
                                <a class="me-2" href="{{ $company->instagram ?? '' }}"><i
                                        class="fa-brands fa-instagram"></i></a>
                                <a class="me-2" href="{{ $company->facebook ?? '' }}"><i
                                        class="fa-brands fa-facebook"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <h6 style="color:#fff">Quick Links</h6>
                    <ul class="list-unstyled mt-2">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('menu') }}">Menu</a></li>
                        <li><a href="{{ route('our-story') }}">Our Story</a></li>
                        <li><a href="{{ route('find-us') }}">Find Us</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3">
                    <h6 style="color:#fff">Menu</h6>
                    <ul class="list-unstyled mt-2">
                        <li><a href="#">Bowls</a></li>
                        <li><a href="#">Burgers</a></li>
                        <li><a href="#">Sandwiches</a></li>
                        <li><a href="#">Sides</a></li>
                        <li><a href="#">Drinks</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h6 style="color:#fff">Contact Us</h6>
                    <p class="mt-2" style="color:#cfcfcf">{{ $company->company_name ?? '' }}
                        <br>{{ $company->address1 ?? '' }}<br>
                        {{ $company->phone1 ?? '' }}<br>
                        {{ $company->email1 ?? '' }}
                    </p>
                </div>
            </div>

            <div class="footer-legal d-flex justify-content-between align-items-center mt-4 flex-wrap">
                <div class="footer-copy mb-2">
                    © {{ date('Y') }} {{ $company->company_name ?? '' }}. All rights reserved.
                </div>

                <div class="footer-links mb-2" style="opacity:.9">
                    Designed & Developed by
                    <a href="https://www.mentosoftware.co.uk">Mento Software</a>
                    &nbsp; &nbsp;
                    <a href="{{ route('privacy-policy') }}">Privacy Policy</a> ·
                    <a href="{{ route('terms-and-conditions') }}">Terms of Service</a>
                </div>

                <div class="footer-payment">
                    <img src="{{ asset('payment-icon.webp') }}" alt="Payment Icons" class="payment-img">
                </div>
            </div>
        </div>
    </footer>

    <style>
        .footer-legal {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 15px;
        }

        .footer-payment {
            margin-left: auto;
        }

        .payment-img {
            height: 30px;
            width: auto;
            display: block;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .footer-payment {
                width: 100%;
                text-align: center;
                margin-top: 12px;
            }
        }
    </style>
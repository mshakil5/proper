    <section class="subscribe-section">
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
                            &nbsp;Subscribe</button>
                    </form>
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
                        <h5 style="margin:0;color:#fff">
                            {{ $company->company_name ?? '' }}
                        </h5>
                        <p style="margin-top:8px;color:#c0c0c0">{{ $company->footer_content }}</p>
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
                    {{ $company->email1 ?? '' }}</p>
            </div>
        </div>

        <div class="footer-legal d-flex justify-content-between align-items-center">
            <div>© {{ date('Y') }}
                {{ $company->company_name ?? '' }}
                . All rights reserved.</div>
            <div style="opacity:.9">Designed & Developed by <a href="https://www.mentosoftware.co.uk">Mento Software</a>
                &nbsp; &nbsp;
                <a href="{{ route('privacy-policy') }}">Privacy Policy</a> · <a
                    href="{{ route('terms-and-conditions') }}">Terms of Service</a></div>
        </div>
    </div>
</footer>
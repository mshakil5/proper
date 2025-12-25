@php
    $ourStory = App\Models\Master::firstOrCreate(['name' => 'our-story']);
@endphp

<section id="our-story" class="section mt-5">
    <div class="container px-3 px-sm-5">
        <div class="row align-items-center">
            <div class="col-md-6 left-collage position-relative ps-lg-4 pe-lg-4 ps-md-3 pe-md-3">
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="collage-item" style="height: 220px;">
                            <img src="https://images.unsplash.com/photo-1528712306091-ed0763094c98?auto=format&fit=crop&w=800&q=80"
                                alt="kitchen-1" class="w-100 h-100">
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="collage-item" style="height: 180px;">
                            <img src="https://images.unsplash.com/photo-1627308595229-7830a5c91f9f?auto=format&fit=crop&w=800&q=80"
                                alt="chef" class="w-100 h-100">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="collage-item" style="height: 180px;">
                            <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=800&q=80"
                                alt="fries" class="w-100 h-100">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="collage-item" style="height: 200px;">
                            <img src="{{ asset('burger.jpg') }}"
                                alt="table" class="w-100 h-100">
                        </div>
                    </div>
                </div>

                <div class="years-badge-overlay">
                    <div class="years-content">
                        <div class="years-number">6+</div>
                        <div class="years-text">Years<br><small>of Excellence</small></div>
                    </div>
                </div>

                <div class="mt-4 text-center text-md-start">
                    <a href="{{ route('our-story') }}" 
                    class="btn-gradient px-4 py-3 fw-bold rounded-pill d-inline-flex align-items-center" 
                    style="text-decoration: none;">
                    Learn More About Us <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <!-- RIGHT: content -->
            <div class="col-md-6 ps-md-5 pe-md-4 ps-lg-5 pe-lg-4 mt-5 mt-md-0">
                {{-- <div class="eyebrow mb-2">{{ $ourStory->short_title ?? '' }}</div> --}}
                <h1 class="story-title mb-4">{{ $ourStory->long_title ?? '' }} <span class="accent">{{ $ourStory->short_description }}</span></h1>
                <p class="story-desc mb-5">{!! $ourStory->long_description ?? '' !!}</p>

                <div class="stats-row mb-5">
                    <div class="stat text-center">
                        <div class="icon pink"><i class="fa-solid fa-heart"></i></div>
                        <div class="num">50k+</div>
                        <div class="label">Happy Customers</div>
                    </div>
                    <div class="stat text-center">
                        <div class="icon yellow"><i class="fa-solid fa-award"></i></div>
                        <div class="num">15+</div>
                        <div class="label">Awards Won</div>
                    </div>
                    <div class="stat text-center">
                        <div class="icon blue"><i class="fa-solid fa-user-group"></i></div>
                        <div class="num">25+</div>
                        <div class="label">Team Members</div>
                    </div>
                </div>

                <div class="journey">
                    <div class="pill"><span class="year">2018</span><span>Started as a small family
                            kitchen</span>
                    </div>
                    <div class="pill"><span class="year">2019</span><span>Opened our first takeaway
                            location</span>
                    </div>
                    <div class="pill"><span class="year">2021</span><span>Expanded to delivery
                            services</span>
                    </div>
                    <div class="pill"><span class="year">2025</span><span>Serving 1000+ happy customers
                            weekly</span></div>
                </div>
            </div>

        </div>
    </div>
</section>
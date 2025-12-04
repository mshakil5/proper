@php
    $findUs = App\Models\Master::firstOrCreate(['name' => 'find-us']);
    $company = App\Models\CompanyDetails::first();
@endphp

<section class="find-us container p-3  align-items-center">
    <div class="eyebrow">{{ $findUs->short_title ?? '' }}</div>
    <h2 class="big-title">{{ $findUs->long_title ?? '' }} <span style="color:var(--orange)">{{ $findUs->short_description ?? '' }}</span></h2>
    <p class="subtitle">{!! $findUs->long_description ?? '' !!}</p>

    <div class="row align-items-start gy-4">
        <div class="col-lg-8">
            <div class="map-card p-3">
                <iframe
                    src="{{ $company->google_map ?? '' }}"
                    width="600" style="height: 450px;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" class="map-img"></iframe>

            </div>
        </div>

        <div class="col-lg-4 info-stack">

            <div class="info-card d-flex align-items-start">
                <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                <div>
                    <h6>Address</h6>
                    <p style="font-size:13px">{{ $company->company_name ?? '' }}
                        <br>{{ $company->address1 ?? '' }}</p>
                </div>
            </div>

            <div class="info-card d-flex align-items-start">
                <div class="icon"><i class="fa-regular fa-clock"></i></div>
                <div>
                    <h6>Opening Hours</h6>
                    <p style="font-size:13px">Mon - Sat: 16:00 - 22:00<br>Sunday: 16:00 - 22:00</p>
                </div>
            </div>

            <div class="info-card d-flex align-items-start">
                <div class="icon"><i class="fa-solid fa-phone"></i></div>
                <div>
                    <h6>Phone</h6>
                    <p style="font-size:13px">{{ $company->phone1 ?? '' }}</p>
                </div>
            </div>

            <div class="delivery-card mt-3">
                <h6 style="margin-bottom:8px">Delivery Zone</h6>
                <p style="margin-bottom:10px;font-size:14px;opacity:0.95">We deliver within a 5-mile radius.
                    Orders
                    over £25 get free delivery!</p>
                <div class="badge">Avg. delivery: 25-35 mins</div>
            </div>

        </div>
    </div>
</section>
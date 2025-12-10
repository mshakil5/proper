@php
    $findUs = App\Models\Master::firstOrCreate(['name' => 'find-us']);
    $company = App\Models\CompanyDetails::first();
@endphp

<section class="find-us container p-3  align-items-center">
    {{-- <div class="eyebrow">{{ $findUs->short_title ?? '' }}</div> --}}
    <h2 class="big-title">{{ $findUs->long_title ?? '' }} <span style="color:var(--orange)">{{ $findUs->short_description ?? '' }}</span></h2>
    <p class="subtitle">{!! $findUs->long_description ?? '' !!}</p>

    <div class="row align-items-start gy-4">
        <div class="col-lg-8">
            <div class="map-card p-3">
                <iframe
                    src="{{ $company->google_map ?? '' }}"
                    width="500" style="height: 450px;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" class="map-img"></iframe>

            </div>
        </div>

        <div class="col-lg-4 info-stack">

            <div class="info-card d-flex align-items-start">
                <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                <div>
                    <h5 class="fw-bold">Address</h5>
                    <p class="company-info">{{ $company->company_name ?? '' }}
                        <br>{{ $company->address1 ?? '' }}</p>
                </div>
            </div>

            <div class="info-card d-flex align-items-start">
                <div class="icon"><i class="fa-regular fa-clock"></i></div>
                <div>
                    <h5 class="fw-bold">Opening Hours</h5>
                    <p class="company-info mb-2">
                        Mon - Sat: 4:30pm - 11:30pm<br>
                        Sunday: 4:30pm - 10:00pm
                    </p>
                    <small style="font-size:12px;">
                        <span style="color:red">*</span> Our online ordering website will stop taking orders ten minutes before we close.
                    </small>
                </div>
            </div>

            <div class="info-card d-flex align-items-start">
                <div class="icon"><i class="fa-solid fa-phone"></i></div>
                <div>
                    <h5 class="fw-bold">Phone</h5>
                    <p class="company-info">{{ $company->phone1 ?? '' }}</p>
                </div>
            </div>

            <div class="delivery-card mt-3">
                <h5 class="fw-bold mb-3" style="margin-bottom:8px;">Minimum Order For Delivery</h5>
                
                <div class="d-flex justify-content-between align-items-center">
                    <p class="order-info mb-0">Minimum Order Within 7.5 Miles</p>
                    <div class="badge bg-warnng text-white" style="padding:0.25rem 0.5rem; font-size:0.85rem;">£15</div>
                </div>

                <small class="d-block mt-2" style="font-size:12px;">
                    <span>*</span> You must spend at least this amount on the items, after discount, excluding any delivery or processing fees.
                </small>
            </div>

        </div>
    </div>
</section>

<style>
    .company-info {
        color: #000 !important;
        font-family: "Segoe UI", Inter, system-ui, -apple-system, "Helvetica Neue", Arial, sans-serif !important;
        font-size: 14px !important;
    }
    .order-info {
        color: #fff !important;
        font-family: "Segoe UI", Inter, system-ui, -apple-system, "Helvetica Neue", Arial, sans-serif !important;
        font-size: 14px !important;
    }
</style>
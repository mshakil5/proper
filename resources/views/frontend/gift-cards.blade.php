@extends('frontend.master')

@section('content')
    <section class="section">
        <div class="container px-5 mx-5">
            <div class="vouchers-header-wrapper my-5">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="eyebrow mb-3">Available</div>
                        <h1 class="big-title mb-0">Our <span class="accent">Gift Cards</span></h1>
                    </div>

                    <div class="col-md-6">
                        <div class="filter-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="filter-input" id="packageSearch" placeholder="Search gift cards...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="vouchers-grid">
                @forelse($packages as $package)
                    <div class="gift-card-container">
                        <div class="gift-card-image-wrapper">
                            @if($package->image)
                                <img src="{{ asset($package->image) }}" alt="{{ $package->name }}" class="gift-card-image">
                            @else
                                <div class="gift-card-placeholder">
                                    <i class="fas fa-gift"></i>
                                </div>
                            @endif
                        </div>

                        <div class="gift-card-details">
                            <div class="gift-card-header">
                                <div class="gift-card-info">
                                    <h5 class="gift-card-name">{{ $package->name }}</h5>
                                    @if($package->description)
                                        <p class="gift-card-desc">{{ $package->description }}</p>
                                    @endif
                                </div>
                                <div class="gift-card-amount">£{{ number_format($package->amount) }}</div>
                            </div>
                        </div>

                            <button class="btn-buy-card" data-package-id="{{ $package->id }}" data-amount="{{ $package->amount }}">
                                <i class="fas fa-shopping-cart"></i>
                                Buy Now
                            </button>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-inbox"></i>
                        <p>No active gift cards</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@section('script')
<script>
$(function() {

    @if(session('success'))
        showSuccess('{{ session("success") }}');
    @endif

    @if(session('error'))
        showError('{{ session("error") }}');
    @endif

    $(document).on('click', '.btn-buy-card', function() {
        let packageId = $(this).data('package-id');
        let amount = $(this).data('amount');
        
        @if(auth()->check())
            initiateGiftCardPayment(packageId, amount);
        @else
            showError('Please login to purchase gift cards');
        @endif
    });

    window.initiateGiftCardPayment = function(packageId, amount) {
        $.ajax({
            url: '{{ route("giftcard.checkout") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                package_id: packageId,
                amount: amount
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirectUrl;
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message ?? 'Failed to process payment');
            }
        });
    };

    $('#packageSearch').on('keyup', function() {
        let searchValue = $(this).val().toLowerCase();
        
        $('.gift-card-container').each(function() {
            let name = $(this).find('.gift-card-name').text().toLowerCase();
            let desc = $(this).find('.gift-card-desc').text().toLowerCase();
            
            if (name.includes(searchValue) || desc.includes(searchValue)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
@endsection
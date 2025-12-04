@extends('frontend.master')

@section('content')
@include('frontend.partials.banner', [
    'title' => 'Book Now',
    'image' => $page->banner_image ?? asset('banner.jpg'),
])

<section class="reservation-section">
    <div class="container">
        <div class="section-title text-center">
            <h2>BOOK YOUR EVENT</h2>
            <p>Fill out the form below to make a reservation for your special event</p>
        </div>

    <form class="php-email-form" method="POST" action="{{ route('booking.store') }}" enctype="multipart/form-data">
    @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="reservation-card">
                    <h3 class="mb-4">Book Now</h3>
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="people" class="form-label">Number of People <span class="text-danger">*</span></label>
                                <select class="form-select @error('people') is-invalid @enderror" id="people" name="people" required>
                                    <option value="" selected disabled>Select</option>
                                    <option value="2">2 People</option>
                                    <option value="4">4 People</option>
                                    <option value="6">6 People</option>
                                    <option value="8">8 People</option>
                                    <option value="10">10+ People</option>
                                </select>
                                @error('people')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" required min="{{ date('Y-m-d') }}">
                                @error('date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="time" class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('time') is-invalid @enderror" id="time" name="time" required>
                                @error('time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" required>
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Upload Order Form (Optional)</label>
                                <input type="file" name="order_form" class="form-control" accept="application/pdf">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Additional Requirements</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="7" placeholder="Any special requests or dietary requirements"></textarea>
                            @error('message')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="captcha-section mb-4 d-flex align-items-center gap-2">
                            <span id="captcha-question" class="fw-bold"></span>
                            <input type="number" id="captcha-answer" class="form-control" style="width: 120px;" placeholder="Answer" required>
                            <span id="captcha-error" class="text-danger d-none"></span>
                        </div>

                        <div class="submit-btn-wrapper">
                        <button type="submit" class="btn submit-btn-solid" id="submit-btn">Book Your Event</button>
                        <div id="loading-text" class="alert alert-info d-none mt-2">Sending your booking...</div>
                        </div>
                        @if(session('success'))
                            <div class="alert alert-success mt-3">{{ session('success') }}</div>
                        @endif

                    <div class="download-section mt-4">
                        <a href="{{ asset('RDT-Catering-Menu-v1.1.pdf') }}" class="download-btn" download><i class="fas fa-download"></i> Download Order Form</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="info-card p-3">
                    <h3 class="mb-3">Menu Selection</h3>

                    <div class="mb-3">
                        <h4 class="fw-bold">Soup</h4>

                        @foreach(['Vegetable','Meat','Pumpkin'] as $item)
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $item }}</span>
                                <div>
                                    <label><input type="radio" name="soup_{{ Str::slug($item,'_') }}" value="yes"> Yes</label>
                                    <label class="ms-2"><input type="radio" name="soup_{{ Str::slug($item,'_') }}" value="no"> No</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <h4 class="fw-bold">Main Dish</h4>

                        @foreach([
                            'Curried Goat','Curried Chicken','Fried Chicken','Jerk Chicken',
                            'Brown Stewed Chicken','Fried Fish','Jerk Pork'
                        ] as $dish)
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $dish }}</span>
                                <div>
                                    <label><input type="radio" name="main_{{ Str::slug($dish,'_') }}" value="yes"> Yes</label>
                                    <label class="ms-2"><input type="radio" name="main_{{ Str::slug($dish,'_') }}" value="no"> No</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <h4 class="fw-bold">Side Dishes</h4>

                        @foreach([
                            'Rice & Peas','Plain Rice','Macaroni Cheese','Dumplings',
                            'Beef Pattie','Chicken Pattie','Lamb Pattie','Vegetable Pattie',
                            'Coleslaw','Salad'
                        ] as $side)
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $side }}</span>
                                <div>
                                    <label><input type="radio" name="side_{{ Str::slug($side,'_') }}" value="yes"> Yes</label>
                                    <label class="ms-2"><input type="radio" name="side_{{ Str::slug($side,'_') }}" value="no"> No</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold">Desserts</h6>

                        @foreach([
                            'Black Forest','Red Velvet','Carrot Cake','Victoria Sponge','Ice Cream'
                        ] as $dessert)
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $dessert }}</span>
                                <div>
                                    <label><input type="radio" name="dessert_{{ Str::slug($dessert,'_') }}" value="yes"> Yes</label>
                                    <label class="ms-2"><input type="radio" name="dessert_{{ Str::slug($dessert,'_') }}" value="no"> No</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </form>

    </div>
</section>

<style>
    .info-card label {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .info-card input[type="radio"] {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #000;
        border-radius: 2px;
        cursor: pointer;
        position: relative;
        display: inline-block;
    }

    .info-card input[type="radio"]:checked::after {
        content: "✔";
        font-size: 15px;
        position: absolute;
        top: -1px;
        left: -1px;
        font-weight: bold;
        line-height: 16px;
    }
</style>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let num1 = Math.floor(Math.random() * 10) + 1;
    let num2 = Math.floor(Math.random() * 10) + 1;
    let correctAnswer = num1 + num2;

    $('#captcha-question').text(`What is ${num1} + ${num2}? *`);

    $('.php-email-form').on('submit', function(e) {
        let userAnswer = parseInt($('#captcha-answer').val());
        if (userAnswer !== correctAnswer) {
            e.preventDefault();
            $('#captcha-error').removeClass('d-none').text('Incorrect answer');
        } else {
            $('#captcha-error').addClass('d-none');
            $('#loading-text').removeClass('d-none');
            $(this).find('button[type="submit"]').prop('disabled', true).text('Sending...');
        }
    });
});
</script>
@endsection
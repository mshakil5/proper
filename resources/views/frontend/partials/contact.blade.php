@php
    $company = App\Models\CompanyDetails::first();
@endphp

<section class="contact-section py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <!-- Left Card: Contact Info -->
            <div class="col-lg-6">
                <div class="card shadow-sm p-4 h-100">
                    <h1>Get in <span style="color:var(--orange)">Touch</span></h1>
                    <p>Have questions or want to place an order? Fill out the form, and we’ll get back to you promptly.</p>

                    <div class="info-card d-flex align-items-start mb-3">
                        <div class="icon me-3"><i class="fa-solid fa-location-dot fa-lg"></i></div>
                        <div>
                            <h6>Address</h6>
                            <p style="font-size:13px">{{ $company->company_name ?? '' }}<br>{{ $company->address1 ?? '' }}</p>
                        </div>
                    </div>

                    <div class="info-card d-flex align-items-start mb-3">
                        <div class="icon me-3"><i class="fa-solid fa-phone fa-lg"></i></div>
                        <div>
                            <h6>Phone</h6>
                            <p style="font-size:13px">{{ $company->phone1 ?? '' }}</p>
                        </div>
                    </div>

                    <div class="info-card d-flex align-items-start">
                        <div class="icon me-3"><i class="fa-solid fa-envelope fa-lg"></i></div>
                        <div>
                            <h6>Email</h6>
                            <p style="font-size:13px">{{ $company->email1 ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card: Contact Form -->
            <div class="col-lg-6">
                <div class="card shadow-sm p-4 h-100">
                    <form action="{{ route('contact.store') }}" method="POST" class="php-email-form" id="contactForm">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="name" class="contact-input" placeholder="Your Name *" required autofocus value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="contact-input" placeholder="Your Email *" required value="{{ old('email') }}">
                                @error('email')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                        </div>
                        <div class="mb-3">
                            <input type="text" name="phone" class="contact-input" placeholder="Your Phone" required value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                        </div>
                        <div class="mb-3">
                            <textarea name="message" class="contact-input" rows="5" placeholder="Your Message *" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Math Captcha -->
                        <div class="mb-3">
                            <label for="captcha-answer" class="form-label fw-bold" id="captcha-question">Loading question...</label>
                            <input type="number" id="captcha-answer" class="contact-input" placeholder="Your Answer *" required>
                            <div id="captcha-error" class="text-danger d-none mt-1"></div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="contact-btn">Send Message</button>
                        </div>

                        <div class="loading mt-2 d-none">Sending...</div>
                        <div class="sent-message mt-2 text-success d-none">Your message has been sent. Thank you!</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.contact-section .card {
    border-radius: 20px;
    background: #fff;
}

.contact-section h3 {
    font-weight: 700;   /* bold */
    font-size: 28px;    /* bigger */
}

.contact-section h3 span {
    font-weight: 700;   /* bold span too */
}

.contact-section p {
    font-weight: 500;   /* medium bold */
    font-size: 16px;    /* slightly bigger */
}

.contact-input {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.1);
    padding: 14px 18px;
    border-radius: 28px;
    color: #000;
    width: 100%;
    outline: none;
    font-weight: 500; /* medium bold */
    font-size: 15px; /* slightly bigger */
    transition: all 0.3s;
}
.contact-input:focus {
    border-color: var(--orange);
    box-shadow: 0 0 10px rgba(255, 165, 0, 0.3);
}

.contact-btn {
    background: var(--orange);
    border: none;
    padding: 12px 28px;
    border-radius: 28px;
    color: #fff;
    font-weight: 700; /* bold */
    font-size: 16px;  /* slightly bigger */
    transition: all 0.3s;
}
.contact-btn:hover {
    opacity: 0.9;
}

#captcha-question {
    font-weight: 600; /* bold */
    font-size: 16px;  /* slightly bigger */
}
</style>
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(function() {
    function generateCaptcha() {
        let num1 = Math.floor(Math.random() * 10) + 1;
        let num2 = Math.floor(Math.random() * 10) + 1;
        return { question: `What is ${num1} + ${num2}? *`, answer: num1 + num2 };
    }

    // Set captcha on page load
    let captcha = generateCaptcha();
    $('#captcha-question').text(captcha.question);

    $('#contactForm').on('submit', function(e) {
        let userAnswer = parseInt($('#captcha-answer').val());
        if (userAnswer !== captcha.answer) {
            e.preventDefault();
            $('#captcha-error').removeClass('d-none').text('Incorrect answer');
            captcha = generateCaptcha(); // regenerate on fail
            $('#captcha-question').text(captcha.question);
            $('#captcha-answer').val('');
        } else {
            $('#captcha-error').addClass('d-none');
            $('.loading').removeClass('d-none');
            $(this).find('button[type="submit"]').prop('disabled', true).text('Sending...');
        }
    });

    @if(session('success'))
        $('.sent-message').removeClass('d-none');
    @endif
});
</script>

@extends('frontend.master')

@section('content')
<section class="container p-3">

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <h2 class="big-title mb-4">
                <span style="color:var(--orange)">Frequently Asked Questions</span>
            </h2>

            <div class="accordion" id="faqAccordion">

                @foreach ($faqs as $key => $faq)
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading{{ $faq->id }}">
                            <button class="accordion-button faq-question {{ $key !== 0 ? 'collapsed' : '' }}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $faq->id }}"
                                aria-expanded="{{ $key === 0 ? 'true' : 'false' }}"
                                aria-controls="collapse{{ $faq->id }}">
                                
                                {{ $faq->question }}
                            </button>
                        </h2>

                        <div id="collapse{{ $faq->id }}" 
                            class="accordion-collapse collapse {{ $key === 0 ? 'show' : '' }}"
                            aria-labelledby="heading{{ $faq->id }}"
                            data-bs-parent="#faqAccordion">

                            <div class="accordion-body faq-answer">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>
    </div>

</section>

<style>
    .faq-question {
        color: var(--orange) !important;
        font-weight: 600;
        padding: 15px 20px !important;
    }

    .accordion-item {
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 12px;
        border: 1px solid #eee;
    }

    .faq-answer {
        padding: 15px 20px;
    }
</style>

@endsection
@if(auth()->user()->getSubscriptionWarningMessage())
<div class="subscription-alert alert-{{ auth()->user()->getSubscriptionWarningMessage()['type'] }}">
    <div class="alert-content">
        <div class="alert-icon">
            <i class="{{ auth()->user()->getSubscriptionWarningMessage()['icon'] }}"></i>
        </div>
        <div class="alert-text">
            <h5 class="alert-title">{{ auth()->user()->getSubscriptionWarningMessage()['title'] }}</h5>
            <p class="alert-message">{{ auth()->user()->getSubscriptionWarningMessage()['message'] }}</p>
        </div>
    </div>
    <a href="{{ route('user.subscription') }}" class="btn-alert-action">
        {{ auth()->user()->getSubscriptionWarningMessage()['action'] }} <i class="fas fa-arrow-right"></i>
    </a>
</div>
@endif
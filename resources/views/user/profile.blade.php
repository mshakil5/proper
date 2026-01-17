@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-user"></i>
        Update Profile
    </h3>
    <p class="dashboard-subtitle">Manage your account information</p>

    <form id="profileForm" class="profile-form">
        @csrf
        
        <div class="form-group">
            <label class="form-label">First Name <span class="required">*</span></label>
            <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" placeholder="Enter first name" required>
        </div>

        <div class="form-group">
            <label class="form-label">Last Name <span class="required">*</span></label>
            <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" placeholder="Enter last name" required>
        </div>

        <div class="form-group">
            <label class="form-label">Email <span class="required">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" placeholder="Enter email" required>
        </div>

        <div class="form-group">
            <label class="form-label">Phone <span class="required">*</span></label>
            <input type="tel" name="phone" class="form-control" value="{{ $user->phone }}" placeholder="Enter phone number" required>
        </div>

        <div class="form-group">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" value="{{ $user->dob }}" placeholder="Enter your date of birth">
            <small class="form-text text-muted">We'll send you a gift on your birthday!</small>
        </div>

        <div class="form-group">
            <label class="form-label">Postal Code</label>
            <input type="text" name="postcode" class="form-control" value="{{ $user->postcode }}" placeholder="Enter postal code">
        </div>

        <button type="submit" class="btn-save-profile" id="submitBtn">Save Changes</button>
    </form>
</div>

@endsection

@section('script')

<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        $('#profileForm').on('submit', function (e) {
            e.preventDefault();

            const $btn = $('#submitBtn');
            const origText = $btn.text();
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('user.update-profile') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    showSuccess(res.message || 'Profile updated successfully!');
                    $btn.prop('disabled', false).text(origText);
                },
                error: function(xhr) {
                    let msg = 'Something went wrong!';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join(' | ');
                        }
                    }
                    showError(msg);
                    $btn.prop('disabled', false).text(origText);
                }
            });
        });
    });
</script>

@endsection
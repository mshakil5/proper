<!-- resources/views/user/password.blade.php -->

@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-lock"></i>
        Change Password
    </h3>
    <p class="dashboard-subtitle">Update your password to keep your account secure</p>

    <form class="password-form">
        <div class="form-group">
            <label class="form-label">Current Password <span class="required">*</span></label>
            <div class="password-wrapper">
                <input type="password" class="form-control" placeholder="Enter current password" id="currentPassword">
                <button type="button" class="password-toggle-btn" onclick="togglePassword('currentPassword')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">New Password <span class="required">*</span></label>
            <div class="password-wrapper">
                <input type="password" class="form-control" placeholder="Enter new password" id="newPassword">
                <button type="button" class="password-toggle-btn" onclick="togglePassword('newPassword')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <small class="form-text">Password must be at least 8 characters</small>
        </div>

        <div class="form-group">
            <label class="form-label">Confirm Password <span class="required">*</span></label>
            <div class="password-wrapper">
                <input type="password" class="form-control" placeholder="Confirm new password" id="confirmPassword">
                <button type="button" class="password-toggle-btn" onclick="togglePassword('confirmPassword')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-save-profile">Update Password</button>
    </form>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}
</script>

@endsection
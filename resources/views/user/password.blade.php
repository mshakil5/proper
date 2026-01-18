@extends('user.master')

@section('user-content')
    <div class="user-dashboard-card">
        <h3 class="dashboard-title">
            <i class="fas fa-lock"></i>
            Change Password
        </h3>
        <p class="dashboard-subtitle">Update your password to keep your account secure</p>

        <form id="passwordForm" class="password-form">
            @csrf

            <div class="form-group">
                <label class="form-label">Current Password <span class="required">*</span></label>
                <div class="password-wrapper">
                    <input type="password" name="current_password" class="form-control" placeholder="Enter current password"
                        required id="currentPassword">
                    <button type="button" class="password-toggle-btn" onclick="togglePassword('currentPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">New Password <span class="required">*</span></label>
                <div class="password-wrapper">
                    <input type="password" name="new_password" class="form-control" placeholder="Enter new password"
                        required id="newPassword">
                    <button type="button" class="password-toggle-btn" onclick="togglePassword('newPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small class="form-text">Password must be at least 6 characters</small>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password <span class="required">*</span></label>
                <div class="password-wrapper">
                    <input type="password" name="new_password_confirmation" class="form-control"
                        placeholder="Confirm new password" required id="confirmPassword">
                    <button type="button" class="password-toggle-btn" onclick="togglePassword('confirmPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-save-profile mt-3" id="submitBtn">Update Password</button>
        </form>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    </script>
@endsection

@section('script')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });

            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();

                const $btn = $('#submitBtn');
                const origText = $btn.text();
                $btn.prop('disabled', true).text('Updating...');

                $.ajax({
                    url: "{{ route('user.update-password') }}", // route to handle password update
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        showSuccess(res.message || 'Password updated successfully!');
                        $btn.prop('disabled', false).text(origText);
                        $('#passwordForm')[0].reset();
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
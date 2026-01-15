<!-- resources/views/user/profile.blade.php -->

@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-user"></i>
        Update Profile
    </h3>
    <p class="dashboard-subtitle">Manage your account information</p>

    <form class="profile-form">
        <div class="form-group">
            <label class="form-label">First Name <span class="required">*</span></label>
            <input type="text" class="form-control" value="John" placeholder="Enter first name">
        </div>

        <div class="form-group">
            <label class="form-label">Last Name <span class="required">*</span></label>
            <input type="text" class="form-control" value="Doe" placeholder="Enter last name">
        </div>

        <div class="form-group">
            <label class="form-label">Email <span class="required">*</span></label>
            <input type="email" class="form-control" value="john@example.com" placeholder="Enter email">
        </div>

        <div class="form-group">
            <label class="form-label">Phone <span class="required">*</span></label>
            <input type="tel" class="form-control" value="+1234567890" placeholder="Enter phone number">
        </div>

        <div class="form-group">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" value="123 Main Street" placeholder="Enter address">
        </div>

        <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" class="form-control" value="New York" placeholder="Enter city">
        </div>

        <div class="form-group">
            <label class="form-label">State</label>
            <input type="text" class="form-control" value="NY" placeholder="Enter state">
        </div>

        <div class="form-group">
            <label class="form-label">Postal Code</label>
            <input type="text" class="form-control" value="10001" placeholder="Enter postal code">
        </div>

        <button type="submit" class="btn-save-profile">Save Changes</button>
    </form>
</div>

@endsection
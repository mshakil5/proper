<!-- resources/views/user/points.blade.php -->

@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-star"></i>
        Reward Points
    </h3>
    <p class="dashboard-subtitle">Track your loyalty points and rewards</p>

    <div class="points-header">
        <div class="points-total">450</div>
        <p class="points-label">Total Points Available</p>
    </div>

    <div class="orders-table-wrapper">
        <table class="points-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Activity</th>
                    <th>Type</th>
                    <th>Points</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jan 15, 2026</td>
                    <td>Order #ORD123456 Completed</td>
                    <td><span class="earned-text">Earned</span></td>
                    <td><span class="points-add">+45</span></td>
                </tr>
                <tr>
                    <td>Jan 12, 2026</td>
                    <td>Referral Bonus</td>
                    <td><span class="earned-text">Earned</span></td>
                    <td><span class="points-add">+100</span></td>
                </tr>
                <tr>
                    <td>Jan 10, 2026</td>
                    <td>Redeemed for Discount</td>
                    <td><span class="redeemed-text">Redeemed</span></td>
                    <td><span class="points-deduct">-50</span></td>
                </tr>
                <tr>
                    <td>Jan 8, 2026</td>
                    <td>Birthday Bonus</td>
                    <td><span class="earned-text">Earned</span></td>
                    <td><span class="points-add">+50</span></td>
                </tr>
                <tr>
                    <td>Jan 5, 2026</td>
                    <td>Order #ORD123454 Completed</td>
                    <td><span class="earned-text">Earned</span></td>
                    <td><span class="points-add">+28</span></td>
                </tr>
                <tr>
                    <td>Jan 1, 2026</td>
                    <td>New Year Bonus</td>
                    <td><span class="earned-text">Earned</span></td>
                    <td><span class="points-add">+25</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
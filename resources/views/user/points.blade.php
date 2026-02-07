@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-star"></i>
        Reward Points
    </h3>
    <p class="dashboard-subtitle">Track your loyalty points and rewards</p>

    <div class="points-header">
        <div class="points-total">{{ auth()->user()->available_points }}</div>
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
                @forelse(auth()->user()->userPoints()->latest()->paginate(10) as $point)
                    <tr>
                        <td>{{ $point->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($point->order_id && $point->order && $point->order->user_id === auth()->id())
                                <a href="{{ route('user.orders.details', $point->order_id) }}" style="text-decoration: none; color: inherit;">
                                    Order #{{ $point->order_id }} Completed
                                </a>
                            @elseif($point->order_id)
                                Order #{{ $point->order_id }} Completed
                            @else
                                Bonus / Adjustment
                            @endif
                        </td>
                        <td>
                            @if($point->point > 0)
                                <span class="earned-text">Earned</span>
                            @else
                                <span class="redeemed-text">Redeemed</span>
                            @endif
                        </td>
                        <td>
                            @if($point->point > 0)
                                <span class="points-add">+{{ $point->point }}</span>
                            @else
                                <span class="points-deduct">{{ $point->point }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No points history available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ auth()->user()->userPoints()->latest()->paginate(10)->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
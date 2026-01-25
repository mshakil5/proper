@extends('admin.pages.master')
@section('title', 'Dashboard')
@section('content')

<div class="container-fluid">
    <!-- Existing dashboard content -->

    <!-- Upcoming Birthdays -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">
                        <i class="fas fa-birthday-cake"></i> Upcoming Customer Birthdays (Next 7 Days)
                    </h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Days Until</th>
                                    <th>Voucher Sent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($upcomingBirthdays->count() > 0)
                                    @foreach($upcomingBirthdays as $user)
                                        <tr>
                                            <td>
                                                <h5 class="fs-14 my-1 fw-normal">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </h5>
                                            </td>
                                            <td>
                                                <h5 class="fs-14 my-1 fw-normal">
                                                    {{ $user->email }}
                                                </h5>
                                            </td>
                                            <td>
                                                <h5 class="fs-14 my-1 fw-normal">
                                                    {{ $user->phone }}
                                                </h5>
                                            </td>
                                            <td>
                                                <h5 class="fs-14 my-1 fw-normal">
                                                    {{ $user->days_until_birthday }} days
                                                </h5>
                                            </td>
                                            <td>
                                                @php
                                                    $birthdayVoucher = $user->coupons()
                                                        ->where('is_birthday_voucher', true)
                                                        ->wherePivot('sent_year', now()->year)
                                                        ->first();
                                                @endphp
                                                
                                                @if($birthdayVoucher)
                                                    <span class="badge bg-success">
                                                        ✓ Sent
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No upcoming birthdays in the next 7 days
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@extends('admin.pages.master')
@section('title', 'Dashboard')
@section('content')

    <div class="container-fluid">

        <!-- Header with Date Filter -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><i class="ri-dashboard-3-line"></i> Dashboard</h4>
                    </div>
                    <form method="GET" action="" class="d-flex gap-2">
                        <input type="date" name="start_date" class="form-control form-control-sm" style="width: 130px;" value="{{ $startDate }}">
                        <input type="date" name="end_date" class="form-control form-control-sm" style="width: 130px;" value="{{ $endDate }}">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="ri-search-line"></i> Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Metrics -->
        <div class="row g-2 mb-3">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <p class="text-muted small mb-1"><i class="ri-money-pound-circle-line"></i> Revenue</p>
                        <h5 class="fw-bold mb-2">£{{ number_format($totalSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <p class="text-muted small mb-1"><i class="ri-shopping-bag-line"></i> Orders</p>
                        <h5 class="fw-bold mb-2">{{ $totalOrders }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body p-3">
                        <p class="text-white-50 small mb-1"><i class="ri-bar-chart-line"></i> Avg Order</p>
                        <h5 class="fw-bold mb-2 text-white">£{{ number_format($avgOrderValue, 2) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <p class="text-muted small mb-1"><i class="ri-user-add-line"></i> New Customers</p>
                        <h5 class="fw-bold mb-2">+{{ $newCustomers }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <p class="text-muted small mb-1"><i class="ri-repeat-2-line"></i> Repeat</p>
                        <h5 class="fw-bold mb-2">{{ $repeatedCustomers }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <p class="text-muted small mb-1"><i class="ri-time-line"></i> Pending</p>
                        <h5 class="fw-bold mb-2">{{ $pendingOrders }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="row g-2">
            <!-- Top Products -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white p-3">
                        <h6 class="mb-0 fw-bold"><i class="ri-star-line"></i> Top Products</h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse($topProducts as $product)
                                    <tr class="border-bottom">
                                        <td class="ps-3 py-2">
                                            <p class="small fw-bold mb-0">{{ $product->product_name }}</p>
                                            <small class="text-muted">{{ $product->total_qty }} orders</small>
                                        </td>
                                        <td class="text-end pe-3"><span class="badge bg-success">£{{ number_format($product->revenue, 2) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">No products sold</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white p-3">
                        <h6 class="mb-0 fw-bold"><i class="ri-shopping-bag-line"></i> Recent Orders</h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-3">Order</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="ps-3"><small><strong>#{{ $order->order_number }}</strong></small></td>
                                        <td><small>{{ $order->first_name }} {{ $order->last_name }}</small></td>
                                        <td><small>£{{ number_format($order->total, 2) }}</small></td>
                                        <td>
                                            @if($order->delivery_type == 'delivery')
                                                <span class="badge bg-info badge-sm">Delivery</span>
                                            @else
                                                <span class="badge bg-secondary badge-sm">Collection</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'primary',
                                                    'preparing' => 'info',
                                                    'ready' => 'secondary',
                                                    'out_for_delivery' => 'secondary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $color = $statusColors[$order->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }} badge-sm">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No orders</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Upcoming Birthdays -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white p-3">
                        <h6 class="mb-0 fw-bold"><i class="ri-cake-line"></i> Birthdays</h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse($upcomingBirthdays as $user)
                                    <tr class="border-bottom">
                                        <td class="ps-3 py-2">
                                            <p class="small fw-bold mb-0">{{ $user->first_name }} {{ $user->last_name }}</p>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </td>
                                        <td class="text-end pe-3">
                                            @php
                                                $birthdayVoucher = $user->coupons()
                                                    ->where('is_birthday_voucher', true)
                                                    ->wherePivot('sent_year', now()->year)
                                                    ->first();
                                            @endphp
                                            @if($birthdayVoucher)
                                                <span class="badge bg-success"><i class="ri-check-line"></i> Sent</span>
                                            @else
                                                <span class="badge bg-warning">{{ $user->days_until_birthday }}d</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">No birthdays</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- Charts Row -->
        <div class="row g-2 mb-3">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white p-3">
                        <h6 class="mb-0 fw-bold"><i class="ri-line-chart-line"></i> Daily Revenue</h6>
                    </div>
                    <div class="card-body p-3" style="height: 280px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white p-3">
                        <h6 class="mb-0 fw-bold"><i class="ri-bank-card-line"></i> Payment Methods</h6>
                    </div>
                    <div class="card-body p-3" style="height: 280px;">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white p-3">
                        <h6 class="mb-0 fw-bold"><i class="ri-time-line"></i> Peak Hours</h6>
                    </div>
                    <div class="card-body p-3" style="height: 280px;">
                        <canvas id="peakChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            const chartData = {!! $chartData !!};
            const paymentData = {!! $paymentData !!};
            const peakData = {!! $peakData !!};

            // Revenue Chart
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: chartData.map(d => d.date),
                    datasets: [{
                        label: 'Revenue (£)',
                        data: chartData.map(d => d.revenue),
                        backgroundColor: '#28a745',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Payment Chart
            new Chart(document.getElementById('paymentChart'), {
                type: 'doughnut',
                data: {
                    labels: paymentData.map(p => p.method),
                    datasets: [{
                        data: paymentData.map(p => p.count),
                        backgroundColor: ['#28a745', '#007bff', '#ff9800']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            // Peak Hours Chart
            new Chart(document.getElementById('peakChart'), {
                type: 'line',
                data: {
                    labels: peakData.map(p => p.hour),
                    datasets: [{
                        label: 'Orders',
                        data: peakData.map(p => p.orders),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0,123,255,0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        });
    </script>
@endsection
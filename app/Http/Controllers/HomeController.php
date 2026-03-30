<?php

namespace App\Http\Controllers;

use App\Mail\BirthdayVoucherMail;
use App\Mail\SubscriptionReminderMail;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\DeliverySubscription;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserPoint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function dashboard()
    {
        if (Auth::check()) {
            $user = auth()->user();

            if ($user->user_type == '1') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function pos()
    {
        $categories = Category::with([
            'products' => function ($q) {
                $q->where('status', 1)
                ->with(['options'])
                ->orderBy('sl', 'asc');
            }
        ])
        ->where('status', 1)
        ->orderBy('sl', 'asc')
        ->get();

        $clients = User::where('user_type', 2)->orderBy('name')->get();

        return view('admin.pos.create', compact('categories', 'clients'));
    }

    public function posGetProduct(Request $request)
    {
        $product = Product::with(['options.items.product'])->find($request->id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json([
            'id' => $product->id,
            'title' => $product->title,
            'price' => $product->price,
            'image' => asset($product->image),
            'has_attribute' => $product->has_attribute,
            'attribute_price' => $product->attribute_price,
            'attribute_name' => $product->attribute_name,
            'has_options' => $product->options()->exists(),
            'sku_ref' => $product->sku_ref,
            'options' => $product->options->map(function($optionGroup) {
                return [
                    'id' => $optionGroup->id,
                    'name' => $optionGroup->name,
                    'required' => $optionGroup->is_required ? 1 : 0,
                    'max' => $optionGroup->max_select,
                    'type' => $optionGroup->type,
                    'items' => $optionGroup->items->sortBy('override_price')->map(function($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->product->title ?? 'Unknown',
                            'price' => $item->override_price ?? 0,
                            'hubrise_option_ref' => $item->hubrise_option_ref ?? '',
                            'product_id' => $item->product_id,
                        ];
                    })->values()
                ];
            })
        ]);
    }

    public function posQuickCustomer(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string',
            'password'   => 'nullable|string|min:6',
        ]);

        $user = User::create([
            'name'       => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => strtolower($request->email),
            'phone'      => preg_replace('/\s+/', '', $request->phone),
            'password'   => Hash::make($request->password ?? 'Password123!'),
            'user_type'  => '2',
            'status'     => 1,
            'image'      => '/placeholder.webp',
            'last_login' => now(),
        ]);

        UserPoint::create([
            'user_id'     => $user->id,
            'order_id'    => null,
            'source'      => 'registration_bonus',
            'point'       => 500,
            'description' => 'Registration bonus points',
        ]);

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
        ]);
    }

    public function adminHome(Request $request)
    {
        // Default: Start of current month to today
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now();

        // Previous period for percentage comparison
        $periodDays = $startDate->diffInDays($endDate);
        $prevStartDate = $startDate->copy()->subDays($periodDays);
        $prevEndDate = $startDate->copy()->subDay();

        // Upcoming Birthdays (not filtered by date range)
        $upcomingBirthdays = User::where('user_type', 2)
            ->whereNotNull('dob')
            ->get()
            ->filter(function ($user) {
                $daysUntil = $user->days_until_birthday;
                return $daysUntil !== null && $daysUntil >= 0 && $daysUntil <= 7;
            })
            ->sortBy('days_until_birthday')
            ->values();

        $this->sendBirthdayVouchers();
        $this->sendSubscriptionReminderEmails();

        // Key Metrics - ALL filtered by date range
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalSales = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        $newCustomers = User::where('user_type', 2)->whereBetween('created_at', [$startDate, $endDate])->count();
        $repeatedCustomers = User::where('user_type', 2)
            ->whereRaw("(SELECT COUNT(*) FROM orders WHERE orders.user_id = users.id AND orders.created_at BETWEEN ? AND ?) > 1", [$startDate, $endDate])
            ->count();

        $pendingOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery'])
            ->count();

        // Previous period metrics for percentage calculation
        $prevTotalOrders = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevTotalSales = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])->sum('total');
        $prevAvgOrder = $prevTotalOrders > 0 ? $prevTotalSales / $prevTotalOrders : 0;
        $prevNewCustomers = User::where('user_type', 2)->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevRepeatedCustomers = User::where('user_type', 2)
            ->whereRaw("(SELECT COUNT(*) FROM orders WHERE orders.user_id = users.id AND orders.created_at BETWEEN ? AND ?) > 1", [$prevStartDate, $prevEndDate])
            ->count();
        $prevPendingOrders = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery'])
            ->count();

        // Calculate percentage changes
        $revenuePercent = $prevTotalSales > 0 ? (($totalSales - $prevTotalSales) / $prevTotalSales) * 100 : 0;
        $ordersPercent = $prevTotalOrders > 0 ? (($totalOrders - $prevTotalOrders) / $prevTotalOrders) * 100 : 0;
        $avgOrderPercent = $prevAvgOrder > 0 ? (($avgOrderValue - $prevAvgOrder) / $prevAvgOrder) * 100 : 0;
        $newCustomersPercent = $prevNewCustomers > 0 ? (($newCustomers - $prevNewCustomers) / $prevNewCustomers) * 100 : 0;
        $repeatedPercent = $prevRepeatedCustomers > 0 ? (($repeatedCustomers - $prevRepeatedCustomers) / $prevRepeatedCustomers) * 100 : 0;
        $pendingPercent = $prevPendingOrders > 0 ? (($pendingOrders - $prevPendingOrders) / $prevPendingOrders) * 100 : 0;

        // Daily Revenue - for chart
        $dailyRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [];
        foreach ($dailyRevenue as $day) {
            $chartData[] = [
                'date' => Carbon::parse($day->date)->format('M d'),
                'revenue' => (float) $day->revenue,
                'orders' => $day->orders
            ];
        }

        // Payment methods breakdown
        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        $paymentData = [];
        foreach ($paymentMethods as $method) {
            $paymentData[] = [
                'method' => ucfirst($method->payment_method),
                'count' => $method->count,
                'total' => (float) $method->total
            ];
        }

        // Top Products - filtered by date range
        $topProducts = OrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->selectRaw('product_name, SUM(quantity) as total_qty, SUM(total) as revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Recent Orders - filtered by date range
        $recentOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Peak hours
        $peakHours = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(total) as revenue')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $peakData = [];
        foreach ($peakHours as $hour) {
            $peakData[] = [
                'hour' => str_pad($hour->hour, 2, '0', STR_PAD_LEFT) . ':00',
                'orders' => $hour->count,
                'revenue' => (float) $hour->revenue
            ];
        }

        return view('admin.pages.dashboard', [
            'upcomingBirthdays' => $upcomingBirthdays,
            'totalOrders' => $totalOrders,
            'totalSales' => $totalSales,
            'avgOrderValue' => $avgOrderValue,
            'newCustomers' => $newCustomers,
            'repeatedCustomers' => $repeatedCustomers,
            'pendingOrders' => $pendingOrders,
            'revenuePercent' => number_format($revenuePercent, 1),
            'ordersPercent' => number_format($ordersPercent, 1),
            'avgOrderPercent' => number_format($avgOrderPercent, 1),
            'newCustomersPercent' => number_format($newCustomersPercent, 1),
            'repeatedPercent' => number_format($repeatedPercent, 1),
            'pendingPercent' => number_format($pendingPercent, 1),
            'chartData' => json_encode($chartData),
            'paymentData' => json_encode($paymentData),
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'peakData' => json_encode($peakData),
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);
    }

    private function sendBirthdayVouchers()
    {
        $tomorrow = \Carbon\Carbon::tomorrow();

        $birthdayUsers = User::where('user_type', 2)
            ->whereNotNull('dob')
            ->get()
            ->filter(function ($user) use ($tomorrow) {
                $dob = \Carbon\Carbon::parse($user->dob);
                return $dob->month === $tomorrow->month && $dob->day === $tomorrow->day;
            });

        $birthdayVoucher = Coupon::where('is_birthday_voucher', true)
            ->where('is_active', true)
            ->first();

        if (!$birthdayVoucher || $birthdayUsers->isEmpty()) {
            return;
        }

        foreach ($birthdayUsers as $user) {
            $alreadySent = $user->coupons()
                ->where('coupon_id', $birthdayVoucher->id)
                ->wherePivot('sent_year', now()->year)
                ->first();

            if (!$alreadySent) {
                $user->coupons()->attach($birthdayVoucher->id, [
                    'sent_at' => now(),
                    'sent_year' => now()->year,
                    'used_count' => 0
                ]);

                Mail::to($user->email)->send(new BirthdayVoucherMail($user, $birthdayVoucher));
            }
        }
    }

    private function sendSubscriptionReminderEmails()
    {
        $sevenDaysFromNow = \Carbon\Carbon::now()->addDays(7)->startOfDay();

        $subscriptions = DeliverySubscription::where('status', 'active')
            ->whereDate('ends_at', $sevenDaysFromNow)
            ->where('sent_7_day_reminder', false)
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;

            if ($user && $user->email) {
                Mail::to($user->email)->send(new SubscriptionReminderMail($user, $subscription, 7));

                $subscription->update(['sent_7_day_reminder' => true]);
            }
        }

        $tomorrowStart = \Carbon\Carbon::tomorrow()->startOfDay();

        $subscriptions = DeliverySubscription::where('status', 'active')
            ->whereDate('ends_at', $tomorrowStart)
            ->where('sent_1_day_reminder', false)
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;

            if ($user && $user->email) {
                Mail::to($user->email)->send(new SubscriptionReminderMail($user, $subscription, 1));

                $subscription->update(['sent_1_day_reminder' => true]);
            }
        }
    }

    public function userHome()
    {
        return view('user.dashboard');
    }
}

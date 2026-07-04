<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $paidStatuses = ['paid', 'preparing', 'shipped', 'delivered'];

        $stats = [
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'orders_total' => Order::count(),
            'sales_total' => Order::whereIn('status', $paidStatuses)->sum('total'),
            'products' => Product::count(),
            'members' => User::where('is_admin', false)->count(),
            'pending' => Order::where('status', 'paid')->count(),
            'inquiries' => Inquiry::where('status', 'pending')->count(),
        ];

        // 최근 14일 일별 매출
        $days = collect(range(13, 0))->map(function ($d) use ($paidStatuses) {
            $date = today()->subDays($d);
            $sum = Order::whereIn('status', $paidStatuses)
                ->whereDate('created_at', $date)
                ->sum('total');
            return ['label' => $date->format('m/d'), 'value' => (int) $sum];
        });
        $chartMax = max(1, $days->max('value'));

        $recentOrders = Order::with('items')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'days', 'chartMax'));
    }
}

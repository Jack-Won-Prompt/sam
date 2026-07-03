<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'orders_total' => Order::count(),
            'sales_total' => Order::where('status', '!=', 'cancelled')->where('status', '!=', 'pending')->sum('total'),
            'products' => Product::count(),
            'members' => User::where('is_admin', false)->count(),
            'pending' => Order::where('status', 'paid')->count(),
        ];

        $recentOrders = Order::with('items')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}

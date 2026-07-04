<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use Illuminate\Http\Request;

class OrderReturnController extends Controller
{
    public function index(Request $request)
    {
        $returns = OrderReturn::with('order', 'user')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.returns.index', [
            'returns' => $returns,
            'statuses' => OrderReturn::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, OrderReturn $return)
    {
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(OrderReturn::STATUSES)),
            'admin_memo' => 'nullable|string|max:500',
        ]);

        $return->update($data);

        return back()->with('success', '처리 상태가 변경되었습니다.');
    }
}

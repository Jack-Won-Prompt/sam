<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = User::withCount('orders')
            ->when($request->q, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->q}%")
                        ->orWhere('email', 'like', "%{$request->q}%")
                        ->orWhere('phone', 'like', "%{$request->q}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function show(User $member)
    {
        $member->load([
            'orders' => fn ($q) => $q->latest(),
            'buyers',
        ]);

        // 이 회원이 구매 대행자로서 진행한 대행 주문
        $agentOrders = $member->is_agent
            ? \App\Models\Order::where('agent_id', $member->id)->with('buyer')->latest()->take(20)->get()
            : collect();

        return view('admin.members.show', compact('member', 'agentOrders'));
    }

    /** 대행자 지정 / 캐쉬백 비율 설정 */
    public function update(Request $request, User $member)
    {
        $data = $request->validate([
            'is_agent' => 'nullable|boolean',
            'cashback_rate' => 'required|integer|min:0|max:100',
        ]);

        $member->update([
            'is_agent' => $request->boolean('is_agent'),
            'cashback_rate' => $data['cashback_rate'],
        ]);

        return back()->with('success', '대행자 설정이 저장되었습니다.');
    }
}

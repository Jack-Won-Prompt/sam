<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /** 구매 대행자 페이지 (미등록 시 신청 안내) */
    public function index()
    {
        $user = auth()->user();

        if (! $user->is_agent) {
            return view('agent.register');
        }

        $buyers = $user->buyers()->get();
        $orders = $user->orders()
            ->whereNotNull('buyer_id')
            ->with('buyer')
            ->latest()
            ->take(10)
            ->get();
        $history = $user->cashbackHistories()->with('order')->take(20)->get();

        return view('agent.index', compact('user', 'buyers', 'orders', 'history'));
    }

    /** 구매 대행자 등록 (셀프 신청 → 즉시 활성) */
    public function register()
    {
        $user = auth()->user();
        if (! $user->is_agent) {
            $user->update([
                'is_agent' => true,
                'cashback_rate' => $user->cashback_rate ?: 5,
            ]);
        }

        return redirect()->route('agent.index')->with('success', '구매 대행자로 등록되었습니다. 구매자를 추가해 주세요.');
    }

    /** 구매자(소매처) 추가 */
    public function storeBuyer(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->is_agent, 403);

        $data = $request->validate([
            'store_name' => 'required|string|max:100',
            'name' => 'required|string|max:50',
            'biz_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
        ]);
        $data['agent_id'] = $user->id;

        Buyer::create($data);

        return back()->with('success', '구매자가 추가되었습니다.');
    }

    /** 구매자 수정 */
    public function updateBuyer(Request $request, Buyer $buyer)
    {
        $this->authorizeBuyer($buyer);

        $data = $request->validate([
            'store_name' => 'required|string|max:100',
            'name' => 'required|string|max:50',
            'biz_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $buyer->update($data);

        return back()->with('success', '구매자 정보가 수정되었습니다.');
    }

    /** 구매자 삭제 */
    public function destroyBuyer(Buyer $buyer)
    {
        $this->authorizeBuyer($buyer);
        $buyer->delete();

        return back()->with('success', '구매자가 삭제되었습니다.');
    }

    protected function authorizeBuyer(Buyer $buyer): void
    {
        abort_unless(auth()->user()->is_agent && $buyer->agent_id === auth()->id(), 403);
    }
}

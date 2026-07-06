<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->get('days', 7);
        if (! in_array($days, [1, 7, 30, 90])) {
            $days = 7;
        }
        $since = today()->subDays($days - 1);

        $base = fn () => PageView::where('created_at', '>=', $since);

        // 요약 지표
        $stats = [
            'pageviews' => (clone $base())->count(),
            'visits' => (clone $base())->distinct('session_id')->count('session_id'),
            'visitors' => (clone $base())->distinct('ip')->count('ip'),
            'today' => PageView::whereDate('created_at', today())->count(),
            'members' => (clone $base())->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];

        // 일별 추이
        $rows = (clone $base())
            ->selectRaw('DATE(created_at) d, COUNT(*) pv, COUNT(DISTINCT session_id) vs')
            ->groupBy('d')->pluck('pv', 'd');
        $sessionsByDay = (clone $base())
            ->selectRaw('DATE(created_at) d, COUNT(DISTINCT session_id) vs')
            ->groupBy('d')->pluck('vs', 'd');

        $trend = collect(range($days - 1, 0))->map(function ($i) use ($rows, $sessionsByDay) {
            $date = today()->subDays($i)->toDateString();
            return [
                'label' => \Illuminate\Support\Carbon::parse($date)->format('m/d'),
                'pv' => (int) ($rows[$date] ?? 0),
                'vs' => (int) ($sessionsByDay[$date] ?? 0),
            ];
        });
        $trendMax = max(1, $trend->max('pv'));

        // 인기 페이지 (경로별)
        $topPages = (clone $base())
            ->selectRaw('path, COUNT(*) c, COUNT(DISTINCT session_id) v')
            ->groupBy('path')->orderByDesc('c')->limit(15)->get()
            ->map(fn ($r) => [
                'path' => $r->path,
                'label' => PageView::labelFor($r->path),
                'count' => (int) $r->c,
                'visitors' => (int) $r->v,
            ]);

        // 유입 경로 (외부 referer)
        $topReferers = (clone $base())
            ->whereNotNull('referer')
            ->selectRaw('referer, COUNT(*) c')
            ->groupBy('referer')->orderByDesc('c')->limit(10)->get()
            ->map(fn ($r) => ['host' => $this->refererHost($r->referer), 'count' => (int) $r->c]);

        // 기기
        $devices = (clone $base())
            ->selectRaw('device, COUNT(*) c')->groupBy('device')->pluck('c', 'device');

        // 최근 방문 세션 (여정 요약)
        $sessions = (clone $base())
            ->selectRaw('session_id, MIN(created_at) first_at, MAX(created_at) last_at, COUNT(*) pages, MAX(user_id) user_id')
            ->whereNotNull('session_id')
            ->groupBy('session_id')
            ->orderByDesc('last_at')
            ->limit(25)->get();

        // 각 세션 진입 페이지
        foreach ($sessions as $s) {
            $entry = PageView::where('session_id', $s->session_id)->orderBy('created_at')->value('path');
            $s->entry_label = PageView::labelFor($entry ?? '/');
            $s->user = $s->user_id ? \App\Models\User::find($s->user_id) : null;
        }

        return view('admin.analytics.index', compact(
            'stats', 'trend', 'trendMax', 'topPages', 'topReferers', 'devices', 'sessions', 'days'
        ));
    }

    /** 세션별 방문 경로(여정) */
    public function session(Request $request)
    {
        $sid = $request->get('id');
        abort_unless($sid, 404);

        $views = PageView::where('session_id', $sid)->orderBy('created_at')->get();
        abort_if($views->isEmpty(), 404);

        $user = $views->first()->user_id ? \App\Models\User::find($views->first()->user_id) : null;

        return view('admin.analytics.session', compact('views', 'sid', 'user'));
    }

    private function refererHost(?string $referer): string
    {
        if (! $referer) {
            return '(직접 유입)';
        }
        $host = parse_url($referer, PHP_URL_HOST) ?: $referer;
        return preg_replace('/^www\./', '', $host);
    }
}

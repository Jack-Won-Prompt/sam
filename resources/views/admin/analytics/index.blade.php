@extends('layouts.admin')

@section('title', '방문 통계')

@section('content')
{{-- 기간 선택 --}}
<div class="flex items-center gap-2 mb-5">
    @foreach ([1 => '오늘', 7 => '7일', 30 => '30일', 90 => '90일'] as $d => $label)
        <a href="{{ route('admin.analytics.index', ['days' => $d]) }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium border {{ $days === $d ? 'border-brand-600 bg-brand-50 text-brand-700' : 'border-neutral-300 text-neutral-600' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- 요약 지표 --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    @php
        $cards = [
            ['페이지뷰', number_format($stats['pageviews']), 'bg-brand-700'],
            ['방문 수(세션)', number_format($stats['visits']), 'bg-brand-600'],
            ['순방문자(IP)', number_format($stats['visitors']), 'bg-brand-500'],
            ['오늘 방문', number_format($stats['today']), 'bg-gold-600'],
            ['로그인 방문자', number_format($stats['members']), 'bg-neutral-700'],
        ];
    @endphp
    @foreach ($cards as [$label, $value, $bg])
        <div class="rounded-xl {{ $bg }} text-white p-5">
            <p class="text-sm text-white/80">{{ $label }}</p>
            <p class="text-2xl font-extrabold mt-2">{{ $value }}</p>
        </div>
    @endforeach
</div>

{{-- 일별 추이 --}}
<div class="bg-white rounded-xl border border-neutral-200 p-6 mb-8">
    <h2 class="font-bold text-neutral-800 mb-5">일별 방문 추이 <span class="text-xs font-normal text-neutral-400">(페이지뷰)</span></h2>
    <div class="flex items-end gap-1.5 h-48">
        @foreach ($trend as $t)
            <div class="flex-1 flex flex-col items-center justify-end h-full group">
                <div class="text-[10px] text-neutral-500 mb-1 opacity-0 group-hover:opacity-100 whitespace-nowrap">PV {{ number_format($t['pv']) }} · 방문 {{ number_format($t['vs']) }}</div>
                <div class="w-full bg-brand-500 hover:bg-brand-600 rounded-t transition-all"
                     style="height: {{ $t['pv'] > 0 ? max(2, round($t['pv'] / $trendMax * 100)) : 0.5 }}%"></div>
                <div class="text-[10px] text-neutral-400 mt-1.5">{{ $t['label'] }}</div>
            </div>
        @endforeach
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-8">
    {{-- 인기 페이지 --}}
    <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-neutral-100"><h2 class="font-bold text-neutral-800">인기 페이지</h2></div>
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 text-neutral-500 text-left">
                <tr><th class="px-5 py-2.5 font-medium">페이지</th><th class="px-5 py-2.5 font-medium text-right">조회</th><th class="px-5 py-2.5 font-medium text-right">방문자</th></tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($topPages as $p)
                    <tr class="hover:bg-neutral-50">
                        <td class="px-5 py-2.5">
                            <span class="font-medium text-neutral-800">{{ $p['label'] }}</span>
                            <span class="block text-xs text-neutral-400 truncate max-w-[220px]">{{ $p['path'] }}</span>
                        </td>
                        <td class="px-5 py-2.5 text-right font-semibold">{{ number_format($p['count']) }}</td>
                        <td class="px-5 py-2.5 text-right text-neutral-500">{{ number_format($p['visitors']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-10 text-center text-neutral-400">데이터가 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 유입 경로 + 기기 --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100"><h2 class="font-bold text-neutral-800">유입 경로</h2></div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($topReferers as $r)
                        <tr class="hover:bg-neutral-50">
                            <td class="px-5 py-2.5 text-neutral-700">{{ $r['host'] }}</td>
                            <td class="px-5 py-2.5 text-right font-semibold">{{ number_format($r['count']) }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-5 py-8 text-center text-neutral-400 text-sm">외부 유입 기록이 없습니다.<br><span class="text-xs">(직접 방문/북마크는 집계되지 않음)</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl border border-neutral-200 p-5">
            <h2 class="font-bold text-neutral-800 mb-3">기기</h2>
            @php $mobile = (int)($devices['mobile'] ?? 0); $desktop = (int)($devices['desktop'] ?? 0); $tot = max(1, $mobile + $desktop); @endphp
            <div class="flex h-4 rounded-full overflow-hidden mb-2">
                <div class="bg-brand-600" style="width: {{ round($desktop/$tot*100) }}%"></div>
                <div class="bg-gold-500" style="width: {{ round($mobile/$tot*100) }}%"></div>
            </div>
            <div class="flex justify-between text-sm text-neutral-600">
                <span>🖥 데스크톱 {{ round($desktop/$tot*100) }}% ({{ number_format($desktop) }})</span>
                <span>📱 모바일 {{ round($mobile/$tot*100) }}% ({{ number_format($mobile) }})</span>
            </div>
        </div>
    </div>
</div>

{{-- 최근 방문 세션 (여정) --}}
<div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-neutral-100">
        <h2 class="font-bold text-neutral-800">최근 방문 <span class="text-xs font-normal text-neutral-400">(세션별 · 클릭 시 이동 경로)</span></h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-left">
            <tr>
                <th class="px-5 py-2.5 font-medium">방문자</th>
                <th class="px-5 py-2.5 font-medium">진입 페이지</th>
                <th class="px-5 py-2.5 font-medium text-center">본 페이지</th>
                <th class="px-5 py-2.5 font-medium">방문 시각</th>
                <th class="px-5 py-2.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse ($sessions as $s)
                <tr class="hover:bg-neutral-50">
                    <td class="px-5 py-2.5">
                        @if ($s->user)
                            <span class="font-medium text-brand-700">{{ $s->user->name }}</span>
                        @else
                            <span class="text-neutral-500">비회원</span>
                        @endif
                    </td>
                    <td class="px-5 py-2.5 text-neutral-700">{{ $s->entry_label }}</td>
                    <td class="px-5 py-2.5 text-center font-semibold">{{ $s->pages }}</td>
                    <td class="px-5 py-2.5 text-neutral-400">{{ \Illuminate\Support\Carbon::parse($s->last_at)->format('m-d H:i') }}</td>
                    <td class="px-5 py-2.5 text-right">
                        <a href="{{ route('admin.analytics.session', ['id' => $s->session_id]) }}" class="text-brand-700 hover:underline text-xs">경로 보기 →</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-neutral-400">방문 기록이 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="text-xs text-neutral-400 mt-4">※ 관리자 페이지·정적파일·봇 요청은 집계에서 제외됩니다. 사이트 내부 이동은 유입경로에 잡히지 않습니다.</p>
@endsection

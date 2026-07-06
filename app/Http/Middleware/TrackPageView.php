<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    /** 수집 제외 경로 접두어 */
    private array $ignore = [
        'admin', 'storage', 'build', 'api', 'livewire', 'up',
        'sitemap.xml', 'robots.txt', 'favicon.ico', 'auth',
        'logout', 'password', 'email',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($this->shouldTrack($request, $response)) {
                $this->record($request);
            }
        } catch (\Throwable $e) {
            // 로깅 실패가 요청에 영향 주지 않도록 무시
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }
        if ($request->ajax() || $request->wantsJson() || $request->isXmlHttpRequest()) {
            return false;
        }
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $type = (string) $response->headers->get('Content-Type');
        if ($type !== '' && ! str_contains($type, 'text/html')) {
            return false;
        }

        $path = trim($request->path(), '/');
        foreach ($this->ignore as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return false;
            }
        }

        // 봇 대략 제외
        $ua = (string) $request->userAgent();
        if ($ua !== '' && preg_match('/bot|crawler|spider|crawling|slurp|facebookexternalhit|preview/i', $ua)) {
            return false;
        }

        return true;
    }

    private function record(Request $request): void
    {
        $ua = (string) $request->userAgent();
        $referer = $request->headers->get('referer');
        // 자기 사이트 내부 이동은 referer 를 null 로(유입경로 집계 정확도)
        if ($referer && str_contains($referer, $request->getHost())) {
            $referer = null;
        }

        PageView::create([
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'user_id' => auth()->id(),
            'path' => '/' . trim($request->path(), '/'),
            'referer' => $referer ? Str::limit($referer, 490, '') : null,
            'ip' => $request->ip(),
            'device' => preg_match('/mobile|android|iphone|ipad/i', $ua) ? 'mobile' : 'desktop',
            'user_agent' => Str::limit($ua, 290, ''),
            'created_at' => now(),
        ]);
    }
}

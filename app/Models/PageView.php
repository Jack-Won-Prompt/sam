<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    public const UPDATED_AT = null; // created_at 만 사용

    protected $fillable = ['session_id', 'user_id', 'path', 'referer', 'ip', 'device', 'user_agent', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** 경로를 보기 좋은 이름으로 */
    public function getLabelAttribute(): string
    {
        return self::labelFor($this->path);
    }

    public static function labelFor(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        return match (true) {
            $path === '/' => '홈',
            str_starts_with($path, '/product/') => '상품상세',
            str_starts_with($path, '/category/') => '카테고리',
            str_starts_with($path, '/collection/') => '기획전/모음',
            str_starts_with($path, '/search') => '검색',
            str_starts_with($path, '/cart') => '장바구니',
            str_starts_with($path, '/checkout') => '주문/결제',
            str_starts_with($path, '/payment') => '결제',
            str_starts_with($path, '/order/complete') => '주문완료',
            str_starts_with($path, '/order/track') => '비회원주문조회',
            str_starts_with($path, '/mypage/orders') => '주문내역',
            str_starts_with($path, '/mypage/wishlist') => '찜',
            str_starts_with($path, '/mypage/points') => '적립금',
            str_starts_with($path, '/support/notices') => '공지사항',
            str_starts_with($path, '/support/faq') => 'FAQ',
            str_starts_with($path, '/support/inquiries') => '1:1문의',
            str_starts_with($path, '/login') => '로그인',
            str_starts_with($path, '/register') => '회원가입',
            default => $path,
        };
    }
}

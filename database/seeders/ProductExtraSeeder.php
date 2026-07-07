<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductExtraSeeder extends Seeder
{
    public function run(): void
    {
        // 카테고리 (없으면 생성)
        $gift = $this->cat('gift', '선물세트', 1);
        $proc = $this->cat('processed', '가공품', 2);
        $etc = $this->cat('etc', '함께 좋은 상품', 3);

        // 기존 샘플 가공품/기타 상품 제거 (실사 이미지 상품으로 대체)
        Product::whereIn('slug', [
            'sanyangsam-juice-30', 'sanyangsam-powder', 'sanyangsam-liquor',
            'gangwon-deodeok', 'doraji-pear-juice',
        ])->get()->each->delete();

        // A. 선물세트 20종 [code, 상품명, 연근(단일), 구성, 가격, best, new]
        $gifts = [
            ['a01', '천년산삼 5년근 5뿌리 한지 선물세트', 5, '5뿌리 · 한지박스+금색보자기', 175000, true, false],
            ['a02', '산양삼 7년근 3뿌리 오동나무 선물세트', 7, '3뿌리 · 오동나무박스+이끼', 195000, false, false],
            ['a03', '산양삼 10년근 2뿌리 프리미엄 오동나무세트', 10, '2뿌리 · 프리미엄 오동나무', 220000, false, true],
            ['a04', '산양삼 6년근 7뿌리 추석 한지 선물세트', 6, '7뿌리 · 한지박스', 340000, true, false],
            ['a05', '산양삼 8년근 5뿌리 전통 보자기 선물세트', 8, '5뿌리 · 전통 보자기', 390000, false, false],
            ['a06', '산양삼 3·5년근 혼합 실속 선물세트', null, '3년근+5년근 혼합구성', 120000, false, false],
            ['a07', '명품 산양삼 7년근 5뿌리 + 진액 10포 (2단)', 7, '7년근 5뿌리 + 산양삼즙 10포', 380000, true, false],
            ['a08', '산양삼 10년근 3뿌리 + 발효환 (2단)', 10, '10년근 3뿌리 + 산양삼환', 350000, false, true],
            ['a09', '산양삼 5년근 20뿌리 대형 명절 선물세트', 5, '20뿌리 · 대형 명절용', 660000, false, false],
            ['a10', '산양삼 6년근 10뿌리 설날 한지 선물세트', 6, '10뿌리 · 한지박스', 460000, false, false],
            ['a11', '산양삼 8년근 3뿌리 오동나무 부모님 선물세트', 8, '3뿌리 · 오동나무박스', 240000, false, false],
            ['a12', '산양삼 12년근 2뿌리 최고급 오동나무세트', 12, '2뿌리 · 최고급 오동나무', 280000, false, true],
            ['a13', '산양삼 15년근 1뿌리 초고급 비단 선물세트', 15, '1뿌리 · 비단 포장', 190000, false, true],
            ['a14', '명품 산양삼 7년근 10뿌리 이끼+보자기 세트', 7, '10뿌리 · 이끼+보자기', 580000, true, false],
            ['a15', '산양삼 5년근 30뿌리 대용량 실속 선물세트', 5, '30뿌리 · 대용량 가성비', 890000, false, false],
            ['a16', '산양삼 6년근 5뿌리 + 홍삼정과 (2단 콤보)', 6, '6년근 5뿌리 + 홍삼정과', 250000, false, false],
            ['a17', '명품 산양삼 10년근 5뿌리 금색보자기 명품세트', 10, '5뿌리 · 금색 보자기', 490000, true, true],
            ['a18', '산양삼 3·5·7년근 각 3뿌리 (3단 구성)', null, '3·5·7년근 각 3뿌리', 330000, false, false],
            ['a19', '산양삼 8년근 7뿌리 추석 명절 한지세트', 8, '7뿌리 · 한지박스', 540000, false, false],
            ['a20', '산양삼 20년근 1뿌리 초희귀 최고급 선물세트', 20, '1뿌리 · 초희귀', 350000, false, true],
        ];
        $sort = 0;
        foreach ($gifts as [$code, $name, $years, $spec, $price, $best, $new]) {
            $this->make($code, $name, $gift->id, $price, $spec, $best, $new, $years ? "{$years}년근" : '혼합', $sort++);
        }

        // B. 가공식품 10종 [code, 상품명, 규격, 가격, best]
        $processed = [
            ['b01', '산양삼 진액즙 (80ml x 30포)', '80ml x 30포', 54000, true],
            ['b02', '산양삼 농축액 엑기스 600ml', '600ml', 89000, false],
            ['b03', '산양삼 발효환 100g', '100g', 68000, false],
            ['b04', '산양삼 순수 분말 50g', '50g', 75000, false],
            ['b05', '산양삼 담금주 세트 (375ml)', '375ml', 95000, false],
            ['b06', '산양삼 차 티백 (30티백)', '1.5g x 30', 32000, false],
            ['b07', '산양삼 진액 스틱 (30포)', '10ml x 30', 59000, true],
            ['b08', '산양삼 꿀절임 200g', '200g', 48000, false],
            ['b09', '산양삼 건조삼 50g', '50g', 120000, false],
            ['b10', '산양삼 발효 진액 (30포)', '70ml x 30', 79000, false],
        ];
        $sort = 0;
        foreach ($processed as [$code, $name, $spec, $price, $best]) {
            $this->make($code, $name, $proc->id, $price, $spec, $best, false, '-', $sort++);
        }

        // C. 함께 좋은 상품 5종
        $etcItems = [
            ['c01', '6년근 홍삼정 240g 선물세트', '240g', 89000, true],
            ['c02', '당귀·황기 한방 선물세트', '한방 혼합', 42000, false],
            ['c03', '강원 아카시아 벌꿀 1kg', '1kg', 32000, false],
            ['c04', '오동나무 산양삼 보관함', '보관함', 59000, false],
            ['c05', '도라지·더덕 선물세트', '혼합 세트', 38000, false],
        ];
        $sort = 0;
        foreach ($etcItems as [$code, $name, $spec, $price, $best]) {
            $this->make($code, $name, $etc->id, $price, $spec, $best, false, '-', $sort++);
        }
    }

    private function cat(string $slug, string $name, int $order): Category
    {
        return Category::updateOrCreate(['slug' => $slug], ['name' => $name, 'parent_id' => null, 'sort_order' => $order, 'is_active' => true]);
    }

    private function make(string $code, string $name, int $catId, int $price, string $spec, bool $best, bool $new, string $years, int $sort): void
    {
        $img = "products/{$code}.jpg";
        Product::updateOrCreate(
            ['slug' => "goods-{$code}"],
            [
                'category_id' => $catId,
                'name' => $name,
                'short_description' => "청정 강원도 산양삼 · {$spec}",
                'description' => $this->desc($name, $spec),
                'origin' => '강원특별자치도',
                'cultivation_years' => $years,
                'weight' => $spec,
                'price' => $price,
                'sale_price' => $best ? (int) round($price * 0.9 / 1000) * 1000 : null,
                'stock' => 50,
                'thumbnail' => file_exists(storage_path('app/public/' . $img)) ? $img : null,
                'is_active' => true,
                'is_best' => $best,
                'is_new' => $new,
                'shipping_fee' => 0,
                'sort_order' => $sort,
            ]
        );
    }

    private function desc(string $name, string $spec): string
    {
        return <<<HTML
<h3>{$name}</h3>
<p>해발 700m 강원도 청정 산속에서 정성껏 재배·가공한 산양삼 제품입니다.</p>
<ul>
  <li><strong>구성/규격</strong> : {$spec}</li>
  <li><strong>원산지</strong> : 강원특별자치도</li>
</ul>
<p>선물용 고급 포장으로 제공되며, 신선/가공 특성에 맞게 보관해 주세요.</p>
HTML;
    }
}

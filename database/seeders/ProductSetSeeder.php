<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSetSeeder extends Seeder
{
    /** 연근별 상품 구성 (연근 => 뿌리수 목록) */
    private array $catalog = [
        3  => [3, 5, 10, 20, 30],
        4  => [2, 6, 15, 50],
        5  => [3, 5, 10, 20, 30],
        6  => [2, 5, 7, 15, 50],
        7  => [3, 5, 10, 20, 30],
        8  => [2, 4, 7, 10, 15],
        10 => [3, 5, 10, 20, 30],
        12 => [2, 3, 5],
        15 => [2, 3],
        20 => [2],
    ];

    /** 연근별 1뿌리 기준가 */
    private array $perRoot = [
        3 => 18000, 4 => 24000, 5 => 33000, 6 => 44000, 7 => 58000,
        8 => 72000, 10 => 95000, 12 => 125000, 15 => 170000, 20 => 260000,
    ];

    /** 베스트(추천) 조합 — 10% 할인 적용 */
    private array $best = ['3-10', '4-6', '5-10', '6-7', '7-10', '8-10', '10-10'];

    public function run(): void
    {
        // 1) 산양삼 하위 카테고리(연근별) 정비
        $parent = Category::firstWhere('slug', 'sanyangsam')
            ?? Category::create(['name' => '산양삼', 'slug' => 'sanyangsam', 'sort_order' => 0, 'is_active' => true]);

        $catMap = [];
        $order = 0;
        foreach (array_keys($this->catalog) as $year) {
            $cat = Category::updateOrCreate(
                ['slug' => "sanyangsam-{$year}"],
                ['name' => "{$year}년근", 'parent_id' => $parent->id, 'sort_order' => $order++, 'is_active' => true]
            );
            $catMap[$year] = $cat->id;
        }

        // 2) 기존 샘플 산양삼/선물세트 상품 제거 (가공품·기타 상품은 유지)
        Product::whereIn('slug', [
            'hongcheon-5year', 'gangwon-3year', 'taebaek-7year',
            'gift-premium-set', 'gift-basic-set',
        ])->get()->each->delete();

        // 3) 상품 생성 (40종)
        $sort = 0;
        foreach ($this->catalog as $year => $rootsList) {
            foreach ($rootsList as $roots) {
                $key = "{$year}-{$roots}";
                $price = $this->perRoot[$year] * $roots;
                $isBest = in_array($key, $this->best);
                $sale = $isBest ? (int) round($price * 0.9 / 1000) * 1000 : null;
                $img = "products/set-{$year}yr-{$roots}.jpg";

                Product::updateOrCreate(
                    ['slug' => "sanyangsam-{$year}yr-{$roots}root"],
                    [
                        'category_id' => $catMap[$year],
                        'name' => "강원 산양삼 {$year}년근 {$roots}뿌리 선물세트",
                        'short_description' => "청정 강원도 산양삼 {$year}년근 {$roots}뿌리 · 이끼 선물세트",
                        'description' => $this->description($year, $roots),
                        'origin' => '강원특별자치도',
                        'cultivation_years' => "{$year}년근",
                        'weight' => "{$roots}뿌리",
                        'price' => $price,
                        'sale_price' => $sale,
                        'stock' => 30,
                        'thumbnail' => file_exists(storage_path('app/public/' . $img)) ? $img : null,
                        'is_active' => true,
                        'is_best' => $isBest,
                        'is_new' => $year >= 10,
                        'shipping_fee' => 0,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }
    }

    private function description(int $year, int $roots): string
    {
        return <<<HTML
<h3>강원 산양삼 {$year}년근 {$roots}뿌리 선물세트</h3>
<p>해발 700m 강원도 청정 산속에서 농약 없이 자연 그대로 재배한 <strong>{$year}년근 산양삼</strong>을
살아있는 이끼와 함께 고급 선물 박스에 정성껏 담았습니다.</p>
<ul>
  <li><strong>연근</strong> : {$year}년근</li>
  <li><strong>구성</strong> : 산양삼 {$roots}뿌리 + 천연 이끼 보관</li>
  <li><strong>재배지</strong> : 강원특별자치도 청정 산지</li>
  <li><strong>포장</strong> : 보냉 선물박스 + 전통 매듭 장식</li>
</ul>
<p>부모님·어른신 선물, 명절 선물로 좋으며, 받으신 후에는 <strong>냉장 보관</strong>하시고
가급적 빠른 시일 내에 드시는 것을 권장합니다.</p>
HTML;
    }
}

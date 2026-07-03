<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- 관리자 / 테스트 회원 ----------
        User::updateOrCreate(
            ['email' => 'admin@sam.test'],
            [
                'name' => '관리자',
                'password' => Hash::make('password'),
                'phone' => '010-0000-0000',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@sam.test'],
            [
                'name' => '홍길동',
                'password' => Hash::make('password'),
                'phone' => '010-1234-5678',
                'postcode' => '25200',
                'address1' => '강원특별자치도 홍천군 홍천읍 산양삼로 1',
                'address2' => '101호',
            ]
        );

        // ---------- 카테고리 ----------
        $categories = [
            ['name' => '산양삼', 'slug' => 'sanyangsam', 'children' => [
                ['name' => '3~4년근', 'slug' => 'sanyangsam-3'],
                ['name' => '5~6년근', 'slug' => 'sanyangsam-5'],
                ['name' => '7년근 이상', 'slug' => 'sanyangsam-7'],
            ]],
            ['name' => '선물세트', 'slug' => 'gift', 'children' => [
                ['name' => '실속형', 'slug' => 'gift-basic'],
                ['name' => '프리미엄', 'slug' => 'gift-premium'],
                ['name' => '명절세트', 'slug' => 'gift-holiday'],
            ]],
            ['name' => '가공품', 'slug' => 'processed', 'children' => [
                ['name' => '산양삼즙', 'slug' => 'processed-juice'],
                ['name' => '산양삼분말', 'slug' => 'processed-powder'],
                ['name' => '산양삼주', 'slug' => 'processed-liquor'],
            ]],
            ['name' => '함께 좋은 상품', 'slug' => 'etc', 'children' => [
                ['name' => '더덕/도라지', 'slug' => 'etc-deodeok'],
                ['name' => '건강즙', 'slug' => 'etc-juice'],
            ]],
        ];

        $catMap = [];
        $order = 0;
        foreach ($categories as $c) {
            $parent = Category::updateOrCreate(
                ['slug' => $c['slug']],
                ['name' => $c['name'], 'sort_order' => $order++, 'is_active' => true]
            );
            $catMap[$c['slug']] = $parent->id;
            $childOrder = 0;
            foreach ($c['children'] ?? [] as $child) {
                $ch = Category::updateOrCreate(
                    ['slug' => $child['slug']],
                    ['name' => $child['name'], 'parent_id' => $parent->id, 'sort_order' => $childOrder++, 'is_active' => true]
                );
                $catMap[$child['slug']] = $ch->id;
            }
        }

        // ---------- 상품 ----------
        $products = [
            [
                'cat' => 'sanyangsam-5', 'name' => '강원 홍천 산양삼 5년근 (실뿌리 포함)',
                'slug' => 'hongcheon-5year',
                'short' => '해발 700m 청정 강원도에서 자연 그대로 키운 5년근 산양삼',
                'origin' => '강원특별자치도 홍천군', 'years' => '5년근', 'weight' => '뿌리당 8~12g',
                'price' => 120000, 'sale' => 98000, 'stock' => 40, 'best' => true, 'new' => false,
                'options' => [['5뿌리', 0], ['10뿌리', 90000], ['20뿌리', 190000]],
            ],
            [
                'cat' => 'sanyangsam-3', 'name' => '강원 산양삼 3년근 실속형',
                'slug' => 'gangwon-3year',
                'short' => '가볍게 즐기는 입문용 산양삼, 매일 건강 관리에 좋아요',
                'origin' => '강원특별자치도 평창군', 'years' => '3년근', 'weight' => '뿌리당 4~6g',
                'price' => 55000, 'sale' => 45000, 'stock' => 80, 'best' => true, 'new' => true,
                'options' => [['5뿌리', 0], ['10뿌리', 40000]],
            ],
            [
                'cat' => 'sanyangsam-7', 'name' => '강원 태백 산양삼 7년근 프리미엄',
                'slug' => 'taebaek-7year',
                'short' => '오랜 세월 깊은 산속에서 자란 7년근, 진하고 깊은 향',
                'origin' => '강원특별자치도 태백시', 'years' => '7년근', 'weight' => '뿌리당 15~20g',
                'price' => 260000, 'sale' => null, 'stock' => 15, 'best' => true, 'new' => false,
                'options' => [['3뿌리 선물함', 0], ['5뿌리 선물함', 160000]],
            ],
            [
                'cat' => 'gift-premium', 'name' => '산양삼 프리미엄 선물세트 (오동나무함)',
                'slug' => 'gift-premium-set',
                'short' => '귀한 분께 드리는 고급 오동나무 선물함 구성',
                'origin' => '강원특별자치도 홍천군', 'years' => '6년근', 'weight' => '10뿌리 구성',
                'price' => 350000, 'sale' => 298000, 'stock' => 20, 'best' => false, 'new' => true,
                'options' => [['6년근 10뿌리', 0], ['7년근 10뿌리', 120000]],
            ],
            [
                'cat' => 'gift-basic', 'name' => '산양삼 실속 선물세트',
                'slug' => 'gift-basic-set',
                'short' => '부담 없는 가격으로 정성을 전하는 실속 구성',
                'origin' => '강원특별자치도 평창군', 'years' => '4년근', 'weight' => '7뿌리 구성',
                'price' => 89000, 'sale' => 79000, 'stock' => 50, 'best' => false, 'new' => false,
                'options' => [['기본 구성', 0]],
            ],
            [
                'cat' => 'processed-juice', 'name' => '산양삼 진액즙 (30포)',
                'slug' => 'sanyangsam-juice-30',
                'short' => '간편하게 마시는 산양삼 진액, 하루 한 포의 건강',
                'origin' => '강원특별자치도 홍천군', 'years' => '-', 'weight' => '80ml x 30포',
                'price' => 68000, 'sale' => 54000, 'stock' => 120, 'best' => true, 'new' => false,
                'options' => [['1박스 (30포)', 0], ['2박스 (60포)', 50000], ['3박스 (90포)', 95000]],
            ],
            [
                'cat' => 'processed-powder', 'name' => '산양삼 분말 100g',
                'slug' => 'sanyangsam-powder',
                'short' => '동결건조 산양삼 분말, 물이나 꿀에 타서 간편하게',
                'origin' => '강원특별자치도 태백시', 'years' => '-', 'weight' => '100g',
                'price' => 75000, 'sale' => null, 'stock' => 60, 'best' => false, 'new' => true,
                'options' => [['100g', 0], ['200g', 68000]],
            ],
            [
                'cat' => 'processed-liquor', 'name' => '산양삼주 담금세트 (5년근 3뿌리)',
                'slug' => 'sanyangsam-liquor',
                'short' => '집에서 직접 담그는 산양삼주, 담금용 유리병 포함',
                'origin' => '강원특별자치도 정선군', 'years' => '5년근', 'weight' => '3뿌리 + 3.6L 병',
                'price' => 95000, 'sale' => 85000, 'stock' => 30, 'best' => false, 'new' => false,
                'options' => [['3뿌리 세트', 0], ['5뿌리 세트', 55000]],
            ],
            [
                'cat' => 'etc-deodeok', 'name' => '강원 자연산 더덕 1kg',
                'slug' => 'gangwon-deodeok',
                'short' => '향이 진한 강원도 더덕, 무침·구이에 좋아요',
                'origin' => '강원특별자치도 인제군', 'years' => '-', 'weight' => '1kg',
                'price' => 32000, 'sale' => 28000, 'stock' => 100, 'best' => false, 'new' => false,
                'options' => [['1kg', 0], ['2kg', 26000]],
            ],
            [
                'cat' => 'etc-juice', 'name' => '도라지 배즙 (30포)',
                'slug' => 'doraji-pear-juice',
                'short' => '환절기 목 건강을 위한 도라지 배즙',
                'origin' => '강원특별자치도 홍천군', 'years' => '-', 'weight' => '100ml x 30포',
                'price' => 39000, 'sale' => 33000, 'stock' => 150, 'best' => false, 'new' => false,
                'options' => [['1박스', 0], ['2박스', 30000]],
            ],
        ];

        $sort = 0;
        foreach ($products as $p) {
            $product = Product::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'category_id' => $catMap[$p['cat']] ?? null,
                    'name' => $p['name'],
                    'short_description' => $p['short'],
                    'description' => $this->buildDescription($p),
                    'origin' => $p['origin'],
                    'cultivation_years' => $p['years'],
                    'weight' => $p['weight'],
                    'price' => $p['price'],
                    'sale_price' => $p['sale'],
                    'stock' => $p['stock'],
                    'thumbnail' => file_exists(storage_path('app/public/products/' . $p['slug'] . '.jpg'))
                        ? 'products/' . $p['slug'] . '.jpg' : null,
                    'is_active' => true,
                    'is_best' => $p['best'],
                    'is_new' => $p['new'],
                    'shipping_fee' => 0,
                    'sort_order' => $sort++,
                ]
            );

            $product->options()->delete();
            $optOrder = 0;
            foreach ($p['options'] as [$optName, $priceAdd]) {
                ProductOption::create([
                    'product_id' => $product->id,
                    'name' => $optName,
                    'price_add' => $priceAdd,
                    'stock' => 999,
                    'sort_order' => $optOrder++,
                    'is_active' => true,
                ]);
            }
        }

        // ---------- 배너 ----------
        Banner::truncate();
        $banners = [
            ['title' => '강원도 청정 산양삼', 'subtitle' => '해발 700m 소나무 숲, 산이 키운 진짜 삼', 'position' => 'main_slider', 'bg_color' => '#1f5fd0', 'image' => 'banners/farm-07.jpg', 'link' => '/category/sanyangsam', 'sort_order' => 0],
            ['title' => '명절 선물, 정성을 담다', 'subtitle' => '프리미엄 산양삼 선물세트 최대 20% 할인', 'position' => 'main_slider', 'bg_color' => '#1c4fab', 'image' => 'banners/farm-08.jpg', 'link' => '/category/gift', 'sort_order' => 1],
            ['title' => '자연 그대로, 무농약 재배', 'subtitle' => '깊은 산속에서 오랜 시간 정성으로', 'position' => 'main_slider', 'bg_color' => '#2570e6', 'image' => 'banners/farm-10.jpg', 'link' => '/category/processed', 'sort_order' => 2],
        ];
        foreach ($banners as $b) {
            $img = $b['image'];
            if (! file_exists(storage_path('app/public/' . $img))) {
                unset($b['image']);
            }
            Banner::create($b + ['is_active' => true]);
        }
    }

    private function buildDescription(array $p): string
    {
        return <<<HTML
<h3>{$p['name']}</h3>
<p>{$p['short']}</p>
<ul>
  <li><strong>재배지역</strong> : {$p['origin']}</li>
  <li><strong>연근</strong> : {$p['years']}</li>
  <li><strong>규격</strong> : {$p['weight']}</li>
</ul>
<p>강원도의 깊은 산속, 해발 고지대의 청정한 환경에서 농약 없이 자연 그대로 재배한 산양삼입니다.
사람의 손길을 최소화하고 산이 키운 그대로의 건강함을 담았습니다.</p>
<p><strong>보관방법</strong> : 신선 산양삼은 냉장 보관하시고 가급적 빠른 시일 내에 드시는 것을 권장합니다.</p>
HTML;
    }
}

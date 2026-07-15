<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

/**
 * 쇼핑몰 메뉴를 연근 6개 라인으로 재구성한다.
 * 6년근 / 6년근특대 / 8년근 / 10년근 / 12년근 / 15년근
 * (기존 카테고리·상품은 모두 비활성화, 가로형 제품 이미지 연결)
 */
class LineupSeeder extends Seeder
{
    /** 연근 라인 정의 */
    private array $lines = [
        [
            'cat' => 'yr-6', 'name' => '6년근', 'years' => '6년근',
            'product' => 'sanyangsam-6yr', 'title' => '강원 산양삼 6년근 (정품)',
            'price' => 90000, 'thumb' => 'lineup/6yr-01.jpg',
            'gallery' => ['lineup/6yr-02.jpg', 'lineup/box-pack.jpg'],
            'desc' => '정직하게 6년을 키운 기본 산양삼. 부담 없이 즐기기 좋은 정사이즈입니다.',
        ],
        [
            'cat' => 'yr-6-special', 'name' => '6년근특대', 'years' => '6년근',
            'product' => 'sanyangsam-6yr-special', 'title' => '강원 산양삼 6년근 특대',
            'price' => 130000, 'thumb' => 'lineup/6yr-special-01.jpg',
            'gallery' => ['lineup/6yr-special-02.jpg', 'lineup/box-pack.jpg'],
            'desc' => '같은 6년근 중에서도 크고 굵게 자란 특대 선별품. 선물용으로 특히 좋습니다.',
        ],
        [
            'cat' => 'yr-8', 'name' => '8년근', 'years' => '8년근',
            'product' => 'sanyangsam-8yr', 'title' => '강원 산양삼 8년근',
            'price' => 190000, 'thumb' => 'lineup/8yr-01.jpg',
            'gallery' => ['lineup/8yr-02.jpg', 'lineup/box-pack.jpg'],
            'desc' => '깊은 산속에서 8년의 시간을 담아낸 산양삼. 진한 향과 실한 뿌리를 자랑합니다.',
        ],
        [
            'cat' => 'yr-10', 'name' => '10년근', 'years' => '10년근',
            'product' => 'sanyangsam-10yr', 'title' => '강원 산양삼 10년근',
            'price' => 260000, 'thumb' => 'lineup/10yr-01.jpg',
            'gallery' => ['lineup/10yr-02.jpg', 'lineup/10yr-03.jpg', 'lineup/box-pack.jpg'],
            'desc' => '10년 이상 산이 키운 프리미엄 산양삼. 귀한 분께 드리는 선물로 손색이 없습니다.',
        ],
        [
            'cat' => 'yr-12', 'name' => '12년근', 'years' => '12년근',
            'product' => 'sanyangsam-12yr', 'title' => '강원 산양삼 12년근',
            'price' => 360000, 'thumb' => 'lineup/12yr-01.jpg',
            'gallery' => ['lineup/12yr-02.jpg', 'lineup/box-pack.jpg'],
            'desc' => '오랜 세월 자연이 빚어낸 12년근. 희소성과 품격을 갖춘 최고급 산양삼입니다.',
        ],
        [
            'cat' => 'yr-15', 'name' => '15년근', 'years' => '15년근',
            'product' => 'sanyangsam-15yr', 'title' => '강원 산양삼 15년근',
            'price' => 520000, 'thumb' => 'lineup/15yr-01.jpg',
            'gallery' => ['lineup/15yr-02.jpg', 'lineup/box-pack.jpg'],
            'desc' => '산삼에 가까운 15년근. 가장 귀하게 대접받는, 명품 중의 명품 산양삼입니다.',
        ],
    ];

    public function run(): void
    {
        // 1) 기존 메뉴/상품 전부 비활성화 (연근 라인만 노출)
        Category::query()->update(['is_active' => false]);
        Product::query()->update(['is_active' => false, 'is_best' => false, 'is_new' => false]);

        $sort = 0;
        foreach ($this->lines as $line) {
            // 2) 최상위 카테고리(그룹 없음)
            $cat = Category::updateOrCreate(
                ['slug' => $line['cat']],
                ['name' => $line['name'], 'parent_id' => null, 'sort_order' => $sort, 'is_active' => true]
            );

            // 3) 대표 상품 1종
            $product = Product::updateOrCreate(
                ['slug' => $line['product']],
                [
                    'category_id' => $cat->id,
                    'name' => $line['title'],
                    'short_description' => "청정 강원도 무농약 산양삼 {$line['name']} · 이끼 선물세트",
                    'description' => $this->description($line),
                    'origin' => '강원특별자치도 횡성',
                    'cultivation_years' => $line['years'],
                    'weight' => '선물세트',
                    'price' => $line['price'],
                    'sale_price' => null,
                    'stock' => 30,
                    'thumbnail' => $line['thumb'],
                    'is_active' => true,
                    'is_best' => false,
                    'is_new' => false,
                    'shipping_fee' => 0,
                    'sort_order' => $sort,
                ]
            );

            // 4) 갤러리 이미지 재구성 (대표 실사컷 + 추가컷)
            ProductImage::where('product_id', $product->id)->delete();
            $gallery = array_merge([$line['thumb']], $line['gallery']);
            foreach ($gallery as $i => $path) {
                if (file_exists(storage_path('app/public/' . $path))) {
                    ProductImage::create(['product_id' => $product->id, 'path' => $path, 'sort_order' => $i]);
                }
            }

            $sort++;
        }
    }

    private function description(array $line): string
    {
        $name = $line['name'];
        $desc = $line['desc'];

        return <<<HTML
<h3>강원 산양삼 {$name}</h3>
<p>{$desc}</p>
<p>해발 700m 강원특별자치도 횡성 청정 소나무 숲에서 <strong>농약 없이</strong> 자연 그대로 재배했습니다.
살아있는 이끼와 함께 고급 선물 케이스에 담아 <strong>황금보자기</strong>로 정성껏 포장해 드립니다.</p>
<ul>
  <li><strong>연근</strong> : {$name}</li>
  <li><strong>재배지</strong> : 강원특별자치도 횡성군 청정 산지</li>
  <li><strong>인증</strong> : 특별관리임산물 품질검사 합격 · 잔류농약 불검출</li>
  <li><strong>포장</strong> : 고급 케이스 + 이끼 + 전통 황금보자기</li>
</ul>
<p>받으신 후에는 <strong>냉장 보관</strong>하시고 가급적 빠른 시일 내에 드시길 권장합니다.</p>
HTML;
    }
}

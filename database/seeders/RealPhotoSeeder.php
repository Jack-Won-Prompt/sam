<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

/**
 * 재배자 제공 실물 사진을 상품 갤러리에 연결한다.
 * (6년근·8년근 특대 실물 + 이끼 보관 참고컷)
 */
class RealPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $sets = [
            'sanyangsam-6' => ['products/real-6yr.jpg', 'products/real-ref.jpg'],
            'sanyangsam-8' => ['products/real-8yr.jpg', 'products/real-ref.jpg'],
        ];

        foreach ($sets as $slug => $paths) {
            $cat = Category::firstWhere('slug', $slug);
            if (! $cat) {
                continue;
            }

            foreach (Product::where('category_id', $cat->id)->get() as $product) {
                foreach ($paths as $i => $path) {
                    if (! file_exists(storage_path('app/public/' . $path))) {
                        continue;
                    }
                    ProductImage::updateOrCreate(
                        ['product_id' => $product->id, 'path' => $path],
                        ['sort_order' => $i],
                    );
                }
            }
        }
    }
}

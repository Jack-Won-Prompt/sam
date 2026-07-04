<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];
        $urls[] = ['loc' => route('home'), 'priority' => '1.0'];
        $urls[] = ['loc' => route('support.notices'), 'priority' => '0.5'];
        $urls[] = ['loc' => route('support.faq'), 'priority' => '0.5'];

        foreach (Category::where('is_active', true)->get() as $cat) {
            $urls[] = ['loc' => route('category.show', $cat), 'priority' => '0.7'];
        }
        foreach (Product::active()->get() as $product) {
            $urls[] = [
                'loc' => route('product.show', $product),
                'priority' => '0.8',
                'lastmod' => $product->updated_at->toAtomString(),
            ];
        }

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /mypage\nDisallow: /checkout\nDisallow: /cart\n\nSitemap: " . url('sitemap.xml') . "\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}

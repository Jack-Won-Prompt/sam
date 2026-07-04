<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'short_description', 'description',
        'origin', 'cultivation_years', 'weight',
        'price', 'sale_price', 'stock', 'thumbnail',
        'is_active', 'is_best', 'is_new', 'shipping_fee', 'sort_order', 'view_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_best' => 'boolean',
        'is_new' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function getAvgRatingAttribute(): float
    {
        return round((float) $this->reviews()->avg('rating'), 1);
    }

    /** 실판매가 (할인가 있으면 할인가) */
    public function getCurrentPriceAttribute(): int
    {
        return $this->sale_price ?? $this->price;
    }

    /** 할인율(%) */
    public function getDiscountRateAttribute(): int
    {
        if (! $this->sale_price || $this->price <= 0) {
            return 0;
        }
        return (int) round(($this->price - $this->sale_price) / $this->price * 100);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

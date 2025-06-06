<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'sku',
        'stock',
        'active',
        'image',
        'category_id',
        'brand_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'stock' => 'integer',
    ];

    /**
     * Set the product's name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? substr($value, 0, 255) : $value,
        );
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to search by name.
     */
    public function scopeSearchByName(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
    }

    /**
     * Scope a query to search products.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $this->scopeSearchByName($query, $search);
    }

    /**
     * Scope a query to filter by categories.
     */
    public function scopeFilterByCategories(Builder $query, array $categoryIds): Builder
    {
        if (empty($categoryIds)) {
            return $query;
        }

        return $query->whereIn('category_id', $categoryIds);
    }

    /**
     * Scope a query to filter by categories (alias).
     */
    public function scopeByCategories(Builder $query, array $categoryIds): Builder
    {
        return $this->scopeFilterByCategories($query, $categoryIds);
    }

    /**
     * Scope a query to filter by brands.
     */
    public function scopeFilterByBrands(Builder $query, array $brandIds): Builder
    {
        if (empty($brandIds)) {
            return $query;
        }

        return $query->whereIn('brand_id', $brandIds);
    }

    /**
     * Scope a query to filter by brands (alias).
     */
    public function scopeByBrands(Builder $query, array $brandIds): Builder
    {
        return $this->scopeFilterByBrands($query, $brandIds);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeFilterByPriceRange(Builder $query, ?float $minPrice, ?float $maxPrice): Builder
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    /**
     * Scope a query to include related models for optimization.
     */
    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['category', 'brand']);
    }

    /**
     * Get the formatted price attribute.
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => 'R$ ' . number_format($this->price, 2, ',', '.')
        );
    }

    /**
     * Check if product is in stock.
     */
    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Check if product is available (active and in stock).
     */
    public function isAvailable(): bool
    {
        return $this->active && $this->inStock();
    }
}

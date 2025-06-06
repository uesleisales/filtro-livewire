<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\BrandRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private BrandRepositoryInterface $brandRepository
    ) {}

    public function getFilteredProducts(
        ?string $search = null,
        $categoryIds = null,
        $brandIds = null,
        int $perPage = 12
    ): LengthAwarePaginator {
        try {
            $query = Product::with(['category', 'brand']);

            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            }

            if ($categoryIds) {
                if (is_array($categoryIds)) {
                    $query->whereIn('category_id', $categoryIds);
                } else {
                    $query->where('category_id', $categoryIds);
                }
            }

            if ($brandIds) {
                if (is_array($brandIds)) {
                    $query->whereIn('brand_id', $brandIds);
                } else {
                    $query->where('brand_id', $brandIds);
                }
            }

            return $query->orderBy('name')
                        ->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error filtering products', [
                'search' => $search,
                'category_ids' => $categoryIds,
                'brand_ids' => $brandIds,
                'error' => $e->getMessage()
            ]);
            
            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    public function getCachedCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->categoryRepository->getAllWithCounts();
    }

    public function getCachedBrands(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->brandRepository->getAllWithCounts();
    }
    
    public function refreshCategoriesCache(): \Illuminate\Database\Eloquent\Collection
    {
        $this->categoryRepository->clearCountsCache();
        return $this->categoryRepository->getAllWithCounts();
    }
    
    public function refreshBrandsCache(): \Illuminate\Database\Eloquent\Collection
    {
        $this->brandRepository->clearCountsCache();
        return $this->brandRepository->getAllWithCounts();
    }

    public function getProductStats()
    {
        return [
            'total' => Product::count(),
            'by_category' => $this->categoryRepository->getAllWithProductCount(),
            'by_brand' => $this->brandRepository->getAllWithProductCount()
        ];
    }



    /**
     * Obter contagem de produtos filtrados
     */
    public function getFilteredProductsCount($search = null, $categories = [], $brands = [])
    {
        $query = Product::query()->where('active', true);

        if (!empty($search)) {
            $query->searchByName($search);
        }

        if (!empty($categories)) {
            $query->filterByCategories($categories);
        }

        if (!empty($brands)) {
            $query->filterByBrands($brands);
        }

        return $query->count();
    }

    /**
     * Limpar cache relacionado aos produtos
     */
    public function clearCache()
    {
        Cache::forget(self::CACHE_CATEGORIES_KEY);
        Cache::forget(self::CACHE_BRANDS_KEY);
        // Em produção, usar tags específicas para cache de produtos
        Cache::flush();
    }
}
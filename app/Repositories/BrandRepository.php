<?php

namespace App\Repositories;

use App\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class BrandRepository implements BrandRepositoryInterface
{
    public function getAllWithProductCount(int $perPage = 15): LengthAwarePaginator
    {
        return Brand::withCount('products')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Brand
    {
        return Cache::remember(
            "brand_{$id}",
            now()->addMinutes(30),
            fn() => Brand::find($id)
        );
    }

    public function findWithProducts(int $id): ?Brand
    {
        return Brand::with(['products' => function ($query) {
            $query->orderBy('name');
        }])->find($id);
    }

    public function create(array $data): Brand
    {
        $brand = Brand::create($data);
        $this->clearCache();
        return $brand;
    }

    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        $this->clearCacheForBrand($brand->id);
        return $brand->fresh();
    }

    public function delete(Brand $brand): bool
    {
        $result = $brand->delete();
        $this->clearCacheForBrand($brand->id);
        return $result;
    }

    public function findBySlug(string $slug): ?Brand
    {
        return Cache::remember(
            "brand_slug_{$slug}",
            now()->addMinutes(30),
            fn() => Brand::where('slug', $slug)->first()
        );
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'brands_all',
            now()->addMinutes(60),
            fn() => Brand::orderBy('name')->get()
        );
    }

    public function getAllWithCounts(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'brands_with_counts',
            now()->addMinutes(5), // Reduzido para 5 minutos
            fn() => Brand::withCount('products')->orderBy('name')->get()
        );
    }
    
    public function clearCountsCache(): void
    {
        Cache::forget('brands_with_counts');
    }

    private function clearCache(): void
    {
        Cache::forget('brands_all');
        Cache::forget('brands_with_counts');
    }

    private function clearCacheForBrand(int $id): void
    {
        Cache::forget("brand_{$id}");
        Cache::forget('brands_all');
        Cache::forget('brands_with_counts');
    }
}
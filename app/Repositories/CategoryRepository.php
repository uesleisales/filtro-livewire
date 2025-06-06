<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllWithProductCount(int $perPage = 15): LengthAwarePaginator
    {
        return Category::withCount('products')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Category
    {
        return Cache::remember(
            "category_{$id}",
            now()->addMinutes(30),
            fn() => Category::find($id)
        );
    }

    public function findWithProducts(int $id): ?Category
    {
        return Category::with(['products' => function ($query) {
            $query->orderBy('name');
        }])->find($id);
    }

    public function create(array $data): Category
    {
        $category = Category::create($data);
        $this->clearCache();
        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        $this->clearCacheForCategory($category->id);
        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        $result = $category->delete();
        $this->clearCacheForCategory($category->id);
        return $result;
    }

    public function findBySlug(string $slug): ?Category
    {
        return Cache::remember(
            "category_slug_{$slug}",
            now()->addMinutes(30),
            fn() => Category::where('slug', $slug)->first()
        );
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'categories_all',
            now()->addMinutes(60),
            fn() => Category::orderBy('name')->get()
        );
    }

    public function getAllWithCounts(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'categories_with_counts',
            now()->addMinutes(5),
            fn() => Category::withCount('products')->orderBy('name')->get()
        );
    }
    
    public function clearCountsCache(): void
    {
        Cache::forget('categories_with_counts');
    }

    private function clearCache(): void
    {
        Cache::forget('categories_all');
        Cache::forget('categories_with_counts');
    }

    private function clearCacheForCategory(int $id): void
    {
        Cache::forget("category_{$id}");
        Cache::forget('categories_all');
        Cache::forget('categories_with_counts');
    }
}
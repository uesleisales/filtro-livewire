<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function getAllWithProductCount(int $perPage = 15): LengthAwarePaginator;
    
    public function findById(int $id): ?Category;
    
    public function findWithProducts(int $id): ?Category;
    
    public function create(array $data): Category;
    
    public function update(Category $category, array $data): Category;
    
    public function delete(Category $category): bool;
    
    public function findBySlug(string $slug): ?Category;
    
    public function getAll(): \Illuminate\Database\Eloquent\Collection;
    
    public function getAllWithCounts(): \Illuminate\Database\Eloquent\Collection;
}
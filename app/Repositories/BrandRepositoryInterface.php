<?php

namespace App\Repositories;

use App\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface
{
    public function getAllWithProductCount(int $perPage = 15): LengthAwarePaginator;
    
    public function findById(string|int $id): ?Brand;
    
    public function findWithProducts(string|int $id): ?Brand;
    
    public function create(array $data): Brand;
    
    public function update(Brand $brand, array $data): Brand;
    
    public function delete(Brand $brand): bool;
    
    public function findBySlug(string $slug): ?Brand;
    
    public function getAll(): \Illuminate\Database\Eloquent\Collection;
    
    public function getAllWithCounts(): \Illuminate\Database\Eloquent\Collection;
}
<?php

namespace App\Services;

use App\Models\Brand;
use App\DTOs\BrandDTO;
use App\Repositories\BrandRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use App\Exceptions\BrandException;
use Illuminate\Support\Collection;

class BrandService
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->brandRepository->getAllWithProductCount($perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching brands', ['error' => $e->getMessage()]);
            throw new BrandException('Failed to fetch brands');
        }
    }

    public function getAllAsDTO(): Collection
    {
        try {
            $brands = $this->brandRepository->getAll();
            return $brands->map(fn($brand) => BrandDTO::fromModel($brand));
        } catch (\Exception $e) {
            Log::error('Error fetching brands as DTO', ['error' => $e->getMessage()]);
            throw new BrandException('Failed to fetch brands');
        }
    }

    public function create(array $data): BrandDTO
    {
        try {
            $brand = $this->brandRepository->create($data);
            return BrandDTO::fromModel($brand);
        } catch (\Exception $e) {
            Log::error('Error creating brand', ['data' => $data, 'error' => $e->getMessage()]);
            throw new BrandException('Failed to create brand');
        }
    }

    public function createFromDTO(BrandDTO $dto): BrandDTO
    {
        try {
            $data = $dto->toCreateArray();
            $brand = $this->brandRepository->create($data);
            return BrandDTO::fromModel($brand);
        } catch (\Exception $e) {
            Log::error('Error creating brand from DTO', ['dto' => $dto->toArray(), 'error' => $e->getMessage()]);
            throw new BrandException('Failed to create brand');
        }
    }

    public function findById(string|int $id): Brand
    {
        try {
            $brand = $this->brandRepository->findById($id);
            if (!$brand) {
                throw new BrandException('Brand not found');
            }
            return $brand;
        } catch (BrandException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error finding brand', ['id' => $id, 'error' => $e->getMessage()]);
            throw new BrandException('Failed to find brand');
        }
    }

    public function findBySlug(string $slug): Brand
    {
        try {
            $brand = $this->brandRepository->findBySlug($slug);
            if (!$brand) {
                throw new BrandException('Brand not found');
            }
            return $brand;
        } catch (BrandException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error finding brand by slug', ['slug' => $slug, 'error' => $e->getMessage()]);
            throw new BrandException('Failed to find brand');
        }
    }

    public function findByIdAsDTO(string|int $id): BrandDTO
    {
        try {
            $brand = $this->findById($id);
            return BrandDTO::fromModel($brand);
        } catch (BrandException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error finding brand as DTO', ['id' => $id, 'error' => $e->getMessage()]);
            throw new BrandException('Failed to find brand');
        }
    }

    public function update(string|int $id, array $data): Brand
    {
        try {
            $brand = $this->findById($id);
            return $this->brandRepository->update($brand, $data);
        } catch (BrandException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating brand', ['id' => $id, 'data' => $data, 'error' => $e->getMessage()]);
            throw new BrandException('Failed to update brand');
        }
    }

    public function updateFromDTO(string|int $id, BrandDTO $dto): BrandDTO
    {
        try {
            $brand = $this->findById($id);
            $data = $dto->toUpdateArray();
            $updatedBrand = $this->brandRepository->update($brand, $data);
            return BrandDTO::fromModel($updatedBrand);
        } catch (BrandException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating brand from DTO', ['id' => $id, 'dto' => $dto->toArray(), 'error' => $e->getMessage()]);
            throw new BrandException('Failed to update brand');
        }
    }

    public function delete(string|int $id): bool
    {
        try {
            $brand = $this->findById($id);
            
            if ($brand->products()->count() > 0) {
                throw new BrandException('Cannot delete brand with associated products');
            }
            
            return $this->brandRepository->delete($brand);
        } catch (BrandException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error deleting brand', ['id' => $id, 'error' => $e->getMessage()]);
            throw new BrandException('Failed to delete brand');
        }
    }

    public function getBrandWithProducts(int $id): Brand
    {
        try {
            return $this->brandRepository->findWithProducts($id);
        } catch (\Exception $e) {
            Log::error('Error fetching brand with products', ['id' => $id, 'error' => $e->getMessage()]);
            throw new BrandException('Failed to fetch brand with products');
        }
    }

    public function getBrandWithProductsAsDTO(int $id): BrandDTO
    {
        try {
            $brand = $this->brandRepository->findWithProducts($id);
            return BrandDTO::fromModel($brand);
        } catch (\Exception $e) {
            Log::error('Error fetching brand with products as DTO', ['id' => $id, 'error' => $e->getMessage()]);
            throw new BrandException('Failed to fetch brand with products');
        }
    }
}
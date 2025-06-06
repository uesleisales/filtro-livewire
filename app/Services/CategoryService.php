<?php

namespace App\Services;

use App\Models\Category;
use App\DTOs\CategoryDTO;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CategoryException;
use Illuminate\Support\Collection;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->categoryRepository->getAllWithProductCount($perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching categories', ['error' => $e->getMessage()]);
            throw new CategoryException('Failed to fetch categories');
        }
    }

    public function getAllAsDTO(): Collection
    {
        try {
            $categories = $this->categoryRepository->getAll();
            return $categories->map(fn($category) => CategoryDTO::fromModel($category));
        } catch (\Exception $e) {
            Log::error('Error fetching categories as DTO', ['error' => $e->getMessage()]);
            throw new CategoryException('Failed to fetch categories');
        }
    }

    public function create(array $data): CategoryDTO
    {
        try {
            $category = $this->categoryRepository->create($data);
            return CategoryDTO::fromModel($category);
        } catch (\Exception $e) {
            Log::error('Error creating category', ['data' => $data, 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to create category');
        }
    }

    public function createFromDTO(CategoryDTO $dto): CategoryDTO
    {
        try {
            $data = $dto->toCreateArray();
            $category = $this->categoryRepository->create($data);
            return CategoryDTO::fromModel($category);
        } catch (\Exception $e) {
            Log::error('Error creating category from DTO', ['dto' => $dto->toArray(), 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to create category');
        }
    }

    public function findById(int $id): Category
    {
        try {
            $category = $this->categoryRepository->findById($id);
            if (!$category) {
                throw new CategoryException('Category not found');
            }
            return $category;
        } catch (CategoryException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error finding category', ['id' => $id, 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to find category');
        }
    }

    public function findBySlug(string $slug): Category
    {
        try {
            $category = $this->categoryRepository->findBySlug($slug);
            if (!$category) {
                throw new CategoryException('Category not found');
            }
            return $category;
        } catch (CategoryException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error finding category by slug', ['slug' => $slug, 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to find category');
        }
    }

    public function findByIdAsDTO(int $id): CategoryDTO
    {
        try {
            $category = $this->findById($id);
            return CategoryDTO::fromModel($category);
        } catch (CategoryException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error finding category as DTO', ['id' => $id, 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to find category');
        }
    }

    public function update(int $id, array $data): Category
    {
        try {
            $category = $this->findById($id);
            return $this->categoryRepository->update($category, $data);
        } catch (CategoryException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating category', ['id' => $id, 'data' => $data, 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to update category');
        }
    }

    public function updateFromDTO(int $id, CategoryDTO $dto): CategoryDTO
    {
        try {
            $category = $this->findById($id);
            $data = $dto->toUpdateArray();
            $updatedCategory = $this->categoryRepository->update($category, $data);
            return CategoryDTO::fromModel($updatedCategory);
        } catch (CategoryException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating category from DTO', ['id' => $id, 'dto' => $dto->toArray(), 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to update category');
        }
    }

    public function delete(int $id): bool
    {
        try {
            $category = $this->findById($id);
            
            if ($category->products()->count() > 0) {
                throw new CategoryException('Cannot delete category with associated products');
            }
            
            return $this->categoryRepository->delete($category);
        } catch (CategoryException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error deleting category', ['id' => $id, 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to delete category');
        }
    }

    public function getCategoryWithProducts(int $id): Category
    {
        try {
            return $this->categoryRepository->findWithProducts($id);
        } catch (\Exception $e) {
            Log::error('Error fetching category with products', ['id' => $id, 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to fetch category with products');
        }
    }

    public function getCategoryWithProductsAsDTO(int $id): CategoryDTO
    {
        try {
            $category = $this->categoryRepository->findWithProducts($id);
            return CategoryDTO::fromModel($category);
        } catch (\Exception $e) {
            Log::error('Error fetching category with products as DTO', ['id' => $id, 'error' => $e->getMessage()]);
            throw new CategoryException('Failed to fetch category with products');
        }
    }
}
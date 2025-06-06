<?php

namespace App\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly float $price,
        public readonly ?string $sku = null,
        public readonly int $stock = 0,
        public readonly bool $active = true,
        public readonly ?string $image = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $brandId = null,
        public readonly ?CategoryDTO $category = null,
        public readonly ?BrandDTO $brand = null,
        public readonly ?\DateTime $createdAt = null,
        public readonly ?\DateTime $updatedAt = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            description: $data['description'] ?? null,
            price: (float) $data['price'],
            sku: $data['sku'] ?? null,
            stock: (int) ($data['stock'] ?? 0),
            active: (bool) ($data['active'] ?? true),
            image: $data['image'] ?? null,
            categoryId: $data['category_id'] ?? null,
            brandId: $data['brand_id'] ?? null,
            category: isset($data['category']) ? CategoryDTO::fromArray($data['category']) : null,
            brand: isset($data['brand']) ? BrandDTO::fromArray($data['brand']) : null,
            createdAt: isset($data['created_at']) ? new \DateTime($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTime($data['updated_at']) : null
        );
    }

    public static function fromModel($model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            price: (float) $model->price,
            sku: $model->sku,
            stock: (int) $model->stock,
            active: (bool) $model->active,
            image: $model->image,
            categoryId: $model->category_id,
            brandId: $model->brand_id,
            category: $model->category ? CategoryDTO::fromModel($model->category) : null,
            brand: $model->brand ? BrandDTO::fromModel($model->brand) : null,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'active' => $this->active,
            'image' => $this->image,
            'category_id' => $this->categoryId,
            'brand_id' => $this->brandId,
            'category' => $this->category?->toArray(),
            'brand' => $this->brand?->toArray(),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }

    public function toCreateArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'active' => $this->active,
            'image' => $this->image,
            'category_id' => $this->categoryId,
            'brand_id' => $this->brandId
        ];
    }

    public function toUpdateArray(): array
    {
        $data = [];
        
        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        
        if ($this->description !== null) {
            $data['description'] = $this->description;
        }
        
        if ($this->price !== null) {
            $data['price'] = $this->price;
        }
        
        if ($this->sku !== null) {
            $data['sku'] = $this->sku;
        }
        
        if ($this->stock !== null) {
            $data['stock'] = $this->stock;
        }
        
        if ($this->active !== null) {
            $data['active'] = $this->active;
        }
        
        if ($this->image !== null) {
            $data['image'] = $this->image;
        }
        
        if ($this->categoryId !== null) {
            $data['category_id'] = $this->categoryId;
        }
        
        if ($this->brandId !== null) {
            $data['brand_id'] = $this->brandId;
        }
        
        return $data;
    }
}
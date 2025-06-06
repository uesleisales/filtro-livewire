<?php

namespace App\DTOs;

class CategoryDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $slug = null,
        public readonly ?int $productsCount = null,
        public readonly ?\DateTime $createdAt = null,
        public readonly ?\DateTime $updatedAt = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            description: $data['description'] ?? null,
            slug: $data['slug'] ?? null,
            productsCount: $data['products_count'] ?? null,
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
            slug: $model->slug,
            productsCount: $model->products_count ?? null,
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
            'slug' => $this->slug,
            'products_count' => $this->productsCount,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }

    public function toCreateArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug
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
        
        if ($this->slug !== null) {
            $data['slug'] = $this->slug;
        }
        
        return $data;
    }
}
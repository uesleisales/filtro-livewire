<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Http\Requests\CategoryRequest;
use App\Exceptions\CategoryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Foundation\Http\FormRequest;

class CategoryController extends AbstractCrudController
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    protected function getService()
    {
        return $this->categoryService;
    }

    protected function getViewPrefix(): string
    {
        return 'categorias';
    }

    protected function getRoutePrefix(): string
    {
        return 'categories';
    }

    protected function getEntityName(): string
    {
        return 'categoria';
    }

    protected function getEntityNamePlural(): string
    {
        return 'categorias';
    }

    protected function getExceptionClass(): string
    {
        return CategoryException::class;
    }

    protected function getWithProductsMethod(): string
    {
        return 'getAllWithProducts';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->categoryService->findBySlug($value);
    }

    public function store(FormRequest $request): RedirectResponse
    {
        return parent::store($request);
    }

    public function update(FormRequest $request, $entity): RedirectResponse
    {
        return parent::update($request, $entity);
    }
}
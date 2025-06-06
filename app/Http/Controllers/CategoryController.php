<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Http\Requests\CategoryRequest;
use App\Exceptions\CategoryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
        return 'categories';
    }

    protected function getRoutePrefix(): string
    {
        return 'categories';
    }

    protected function getEntityName(): string
    {
        return 'category';
    }

    protected function getEntityNamePlural(): string
    {
        return 'categories';
    }

    protected function getExceptionClass(): string
    {
        return CategoryException::class;
    }

    protected function getWithProductsMethod(): string
    {
        return 'getCategoryWithProducts';
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        return parent::store($request);
    }

    public function update(CategoryRequest $request, int $id): RedirectResponse
    {
        return parent::update($request, $id);
    }
}
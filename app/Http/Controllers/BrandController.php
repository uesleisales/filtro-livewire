<?php

namespace App\Http\Controllers;

use App\Services\BrandService;
use App\Http\Requests\BrandRequest;
use App\Exceptions\BrandException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Foundation\Http\FormRequest;

class BrandController extends AbstractCrudController
{
    public function __construct(
        private BrandService $brandService
    ) {}

    protected function getService()
    {
        return $this->brandService;
    }

    protected function getViewPrefix(): string
    {
        return 'marcas';
    }

    protected function getRoutePrefix(): string
    {
        return 'brands';
    }

    protected function getEntityName(): string
    {
        return 'marca';
    }

    protected function getEntityNamePlural(): string
    {
        return 'marcas';
    }

    protected function getExceptionClass(): string
    {
        return BrandException::class;
    }

    protected function getWithProductsMethod(): string
    {
        return 'getAllWithProducts';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->brandService->findBySlug($value);
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
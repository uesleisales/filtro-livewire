<?php

namespace App\Http\Controllers;

use App\Services\BrandService;
use App\Http\Requests\BrandRequest;
use App\Exceptions\BrandException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
        return 'brands';
    }

    protected function getRoutePrefix(): string
    {
        return 'brands';
    }

    protected function getEntityName(): string
    {
        return 'brand';
    }

    protected function getEntityNamePlural(): string
    {
        return 'brands';
    }

    protected function getExceptionClass(): string
    {
        return BrandException::class;
    }

    protected function getWithProductsMethod(): string
    {
        return 'getBrandWithProducts';
    }

    public function store(BrandRequest $request): RedirectResponse
    {
        return parent::store($request);
    }

    public function update(BrandRequest $request, int $id): RedirectResponse
    {
        return parent::update($request, $id);
    }
}
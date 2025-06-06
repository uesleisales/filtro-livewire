<?php

namespace App\Livewire;

use App\Services\ProductService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Log;

class ProductFilter extends Component
{
    use WithPagination;

    #[Url(as: 'busca', except: '')]
    public $searchName = '';
    
    #[Url(as: 'categorias', except: [])]
    public $selectedCategories = [];
    
    #[Url(as: 'marcas', except: [])]
    public $selectedBrands = [];
    
    #[Url(as: 'por_pagina', except: 12)]
    public $perPage = 12;
    
    public $showFilters = true;
    public $searchBuffer = '';
    
    protected $listeners = ['clearFilters', 'applySearch'];
    
    public $categories;
    public $brands;
    
    public function mount(ProductService $productService)
    {
        try {
            $this->categories = $productService->getCachedCategories();
            $this->brands = $productService->getCachedBrands();
            $this->validateUrlParameters();
            $this->searchBuffer = $this->searchName;
        } catch (\Exception $e) {
            Log::error('Error in ProductFilter@mount', ['error' => $e->getMessage()]);
            $this->categories = collect();
            $this->brands = collect();
        }
    }
    
    public function getCategoriesWithCountsProperty()
    {
        return $this->categories;
    }
    
    public function getBrandsWithCountsProperty()
    {
        return $this->brands;
    }
    
    private function validateUrlParameters()
    {
        if (!empty($this->selectedCategories)) {
            $validCategories = $this->categories->pluck('id')->toArray();
            $this->selectedCategories = array_intersect($this->selectedCategories, $validCategories);
        }
        
        if (!empty($this->selectedBrands)) {
            $validBrands = $this->brands->pluck('id')->toArray();
            $this->selectedBrands = array_intersect($this->selectedBrands, $validBrands);
        }
        
        if (!in_array($this->perPage, [6, 12, 24, 48])) {
            $this->perPage = 12;
        }
        
        $this->searchName = trim(strip_tags($this->searchName));
    }
    
    /**
     * Resetar paginação quando filtros mudarem
     */
    public function updatedSearchName()
    {
        $this->resetPage();
        $this->dispatch('filtersApplied');
    }
    
    public function updatedSelectedCategories()
    {
        $this->resetPage();
        $this->dispatch('filtersApplied');
    }
    
    public function updatedSelectedBrands()
    {
        $this->resetPage();
        $this->dispatch('filtersApplied');
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    /**
     * Atualizar buffer de busca (para debounce)
     */
    public function updatedSearchBuffer()
    {
        // O debounce será implementado no frontend via JavaScript
        // Este método serve para capturar mudanças no buffer
    }
    
    /**
     * Aplicar busca após debounce
     */
    public function applySearch()
    {
        $this->searchName = $this->searchBuffer;
        $this->resetPage();
        $this->dispatch('filtersApplied');
    }
    
    /**
     * Limpar todos os filtros
     */
    public function clearFilters()
    {
        $this->searchName = '';
        $this->searchBuffer = '';
        $this->selectedCategories = [];
        $this->selectedBrands = [];
        $this->resetPage();
        
        // Emitir evento para atualizar URL
        $this->dispatch('filtersCleared');
    }
    
    /**
     * Alternar visibilidade dos filtros
     */
    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
        
        if ($this->showFilters) {
            $this->refreshCategoriesAndBrands();
        }
    }
    
    public function refreshCategoriesAndBrands()
    {
        try {
            $productService = app(ProductService::class);
            $this->categories = $productService->refreshCategoriesCache();
            $this->brands = $productService->refreshBrandsCache();
        } catch (\Exception $e) {
            Log::error('Error in ProductFilter@refreshCategoriesAndBrands', ['error' => $e->getMessage()]);
        }
    }
    
    public function getProductsProperty()
    {
        try {
            $productService = app(ProductService::class);
            return $productService->getFilteredProducts(
                $this->searchName,
                $this->selectedCategories,
                $this->selectedBrands,
                $this->perPage
            );
        } catch (\Exception $e) {
            Log::error('Error in ProductFilter@getProductsProperty', ['error' => $e->getMessage()]);
            return collect();
        }
    }
    
    public function getProductsCountProperty()
    {
        try {
            $productService = app(ProductService::class);
            return $productService->getFilteredProductsCount(
                $this->searchName,
                $this->selectedCategories,
                $this->selectedBrands
            );
        } catch (\Exception $e) {
            Log::error('Error in ProductFilter@getProductsCountProperty', ['error' => $e->getMessage()]);
            return 0;
        }
    }
    
    /**
     * Verificar se há filtros ativos
     */
    public function getHasActiveFiltersProperty()
    {
        return !empty($this->searchName) || 
               !empty($this->selectedCategories) || 
               !empty($this->selectedBrands);
    }
    
    /**
     * Obter opções de paginação
     */
    public function getPerPageOptionsProperty()
    {
        return [
            6 => '6 por página',
            12 => '12 por página',
            24 => '24 por página',
            48 => '48 por página'
        ];
    }
    
    public function clearFilterCache()
    {
        try {
            $productService = app(ProductService::class);
            $productService->clearCache();
        } catch (\Exception $e) {
            Log::error('Error in ProductFilter@clearFilterCache', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Exportar filtros atuais para URL
     */
    public function getFilterUrlProperty()
    {
        $params = [];
        
        if (!empty($this->searchName)) {
            $params['busca'] = $this->searchName;
        }
        
        if (!empty($this->selectedCategories)) {
            $params['categorias'] = $this->selectedCategories;
        }
        
        if (!empty($this->selectedBrands)) {
            $params['marcas'] = $this->selectedBrands;
        }
        
        if ($this->perPage !== 12) {
            $params['por_pagina'] = $this->perPage;
        }
        
        return request()->url() . '?' . http_build_query($params);
    }
    
    /**
     * Renderizar o componente
     */
    public function render()
    {
        return view('livewire.product-filter', [
            'products' => $this->products,
            'productsCount' => $this->productsCount,
            'hasActiveFilters' => $this->hasActiveFilters,
            'perPageOptions' => $this->perPageOptions,
            'filterUrl' => $this->filterUrl,
            'categories' => $this->categoriesWithCounts,
            'brands' => $this->brandsWithCounts,
        ]);
    }
}

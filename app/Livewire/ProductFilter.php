<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Cache;

class ProductFilter extends Component
{
    use WithPagination;

    // Propriedades reativas para filtros com persistência na URL
    #[Url(as: 'busca', except: '')]
    public $searchName = '';
    
    #[Url(as: 'categorias', except: [])]
    public $selectedCategories = [];
    
    #[Url(as: 'marcas', except: [])]
    public $selectedBrands = [];
    
    // Propriedades para controle da interface
    #[Url(as: 'por_pagina', except: 12)]
    public $perPage = 12;
    
    public $showFilters = true;
    
    // Propriedades para debounce
    public $searchBuffer = '';
    
    // Listeners para eventos
    protected $listeners = ['clearFilters', 'applySearch'];
    
    // Propriedades computadas para otimização
    public $categories;
    public $brands;
    
    // Cache keys
    private const CACHE_CATEGORIES = 'filter_categories';
    private const CACHE_BRANDS = 'filter_brands';
    private const CACHE_TTL = 3600; // 1 hora
    
    /**
     * Inicialização do componente
     */
    public function mount()
    {
        // Carregar categorias e marcas básicas (sem contadores fixos)
        $this->categories = Cache::remember(self::CACHE_CATEGORIES, self::CACHE_TTL, function () {
            return Category::orderBy('name')->get();
        });
        
        $this->brands = Cache::remember(self::CACHE_BRANDS, self::CACHE_TTL, function () {
            return Brand::orderBy('name')->get();
        });
        
        // Validar parâmetros da URL
        $this->validateUrlParameters();
        
        // Inicializar buffer de busca
        $this->searchBuffer = $this->searchName;
    }
    
    /**
     * Obter categorias com contadores dinâmicos baseados nos filtros atuais
     */
    public function getCategoriesWithCountsProperty()
    {
        return $this->categories->map(function ($category) {
            // Criar query base para produtos ativos
            $query = Product::query()->where('active', true);
            
            // Aplicar filtro de categoria específica
            $query->where('category_id', $category->id);
            
            // Aplicar outros filtros ativos (exceto categoria)
            if (!empty($this->selectedBrands)) {
                $query->whereIn('brand_id', $this->selectedBrands);
            }
            
            if (!empty($this->searchName)) {
                $query->searchByName($this->searchName);
            }
            
            $category->products_count = $query->count();
            return $category;
        });
    }
    
    /**
     * Obter marcas com contadores dinâmicos baseados nos filtros atuais
     */
    public function getBrandsWithCountsProperty()
    {
        return $this->brands->map(function ($brand) {
            // Criar query base para produtos ativos
            $query = Product::query()->where('active', true);
            
            // Aplicar filtro de marca específica
            $query->where('brand_id', $brand->id);
            
            // Aplicar outros filtros ativos (exceto marca)
            if (!empty($this->selectedCategories)) {
                $query->whereIn('category_id', $this->selectedCategories);
            }
            
            if (!empty($this->searchName)) {
                $query->searchByName($this->searchName);
            }
            
            $brand->products_count = $query->count();
            return $brand;
        });
    }
    
    /**
     * Validar parâmetros vindos da URL
     */
    private function validateUrlParameters()
    {
        // Validar categorias selecionadas
        if (!empty($this->selectedCategories)) {
            $validCategories = $this->categories->pluck('id')->toArray();
            $this->selectedCategories = array_intersect($this->selectedCategories, $validCategories);
        }
        
        // Validar marcas selecionadas
        if (!empty($this->selectedBrands)) {
            $validBrands = $this->brands->pluck('id')->toArray();
            $this->selectedBrands = array_intersect($this->selectedBrands, $validBrands);
        }
        
        // Validar perPage
        if (!in_array($this->perPage, [6, 12, 24, 48])) {
            $this->perPage = 12;
        }
        
        // Sanitizar searchName
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
    }
    
    /**
     * Gerar chave de cache para consulta
     */
    private function getCacheKey($suffix = '')
    {
        $filters = [
            'search' => $this->searchName,
            'categories' => $this->selectedCategories,
            'brands' => $this->selectedBrands,
            'page' => $this->getPage(),
            'perPage' => $this->perPage
        ];
        
        return 'products_filter_' . md5(serialize($filters)) . $suffix;
    }
    
    /**
     * Construir query base para produtos
     */
    private function buildProductQuery()
    {
        return Product::query()
            ->with(['category:id,name,slug', 'brand:id,name,slug']) // Eager loading otimizado
            ->when($this->searchName, function ($query) {
                $query->searchByName($this->searchName);
            })
            ->when($this->selectedCategories, function ($query) {
                $query->filterByCategories($this->selectedCategories);
            })
            ->when($this->selectedBrands, function ($query) {
                $query->filterByBrands($this->selectedBrands);
            })
            ->where('active', true);
    }
    
    /**
     * Obter produtos filtrados
     */
    public function getProductsProperty()
    {
        return $this->buildProductQuery()
            ->orderBy('name')
            ->paginate($this->perPage);
    }
    
    /**
     * Obter contagem total de produtos filtrados
     */
    public function getProductsCountProperty()
    {
        return $this->buildProductQuery()->count();
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
    
    /**
     * Limpar cache relacionado aos filtros
     */
    public function clearFilterCache()
    {
        // Limpar cache de categorias e marcas
        Cache::forget(self::CACHE_CATEGORIES);
        Cache::forget(self::CACHE_BRANDS);
        
        // Limpar cache de produtos (padrão genérico)
        $tags = ['products_filter_*'];
        foreach ($tags as $tag) {
            Cache::flush(); // Em produção, usar tags específicas
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

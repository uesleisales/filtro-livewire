<div class="container-fluid py-4">
    {{-- Header com título e controles --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fas fa-search me-2"></i>
                Catálogo de Produtos
            </h2>
            <p class="text-muted mb-0">Encontre produtos usando os filtros abaixo</p>
        </div>
        <div class="col-md-4 text-end">
            <button 
                wire:click="toggleFilters" 
                class="btn btn-outline-primary btn-sm"
                type="button"
            >
                <i class="fas {{ $showFilters ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>
                {{ $showFilters ? 'Ocultar' : 'Mostrar' }} Filtros
            </button>
        </div>
    </div>

    {{-- Seção de Filtros --}}
    @if($showFilters)
    <div class="card shadow-sm mb-4" wire:transition>
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Filtros de Busca
                    </h5>
                </div>
                <div class="col-auto">
                    @if($hasActiveFilters)
                        <button 
                            wire:click="clearFilters" 
                            class="btn btn-outline-danger btn-sm"
                            type="button"
                        >
                            <i class="fas fa-times me-1"></i>
                            Limpar Filtros
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Campo de busca por nome --}}
                <div class="col-md-12">
                    <label for="searchBuffer" class="form-label fw-semibold">
                        <i class="fas fa-search me-1"></i>
                        Buscar por nome do produto
                    </label>
                    <input 
                        wire:model.live.debounce.500ms="searchBuffer"
                        type="text" 
                        class="form-control" 
                        id="searchBuffer"
                        placeholder="Digite o nome do produto..."
                        x-data="{ searchTimeout: null }"
                        x-on:input="
                            clearTimeout(searchTimeout);
                            searchTimeout = setTimeout(() => {
                                $wire.call('applySearch');
                            }, 800);
                        "
                    >
                    <small class="text-muted">A busca será aplicada automaticamente após parar de digitar</small>
                </div>

                {{-- Filtro por categorias --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-tags me-1"></i>
                        Categorias
                        @if(count($selectedCategories) > 0)
                            <span class="badge bg-primary ms-1">{{ count($selectedCategories) }}</span>
                        @endif
                    </label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @forelse($categories as $category)
                            <div class="form-check">
                                <input 
                                    wire:model.live="selectedCategories"
                                    class="form-check-input" 
                                    type="checkbox" 
                                    value="{{ $category->id }}"
                                    id="category_{{ $category->id }}"
                                >
                                <label class="form-check-label" for="category_{{ $category->id }}">
                                    {{ $category->name }}
                                    <small class="text-muted">({{ $category->products_count ?? 0 }})</small>
                                </label>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Nenhuma categoria encontrada</p>
                        @endforelse
                    </div>
                </div>

                {{-- Filtro por marcas --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-copyright me-1"></i>
                        Marcas
                        @if(count($selectedBrands) > 0)
                            <span class="badge bg-primary ms-1">{{ count($selectedBrands) }}</span>
                        @endif
                    </label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @forelse($brands as $brand)
                            <div class="form-check">
                                <input 
                                    wire:model.live="selectedBrands"
                                    class="form-check-input" 
                                    type="checkbox" 
                                    value="{{ $brand->id }}"
                                    id="brand_{{ $brand->id }}"
                                >
                                <label class="form-check-label" for="brand_{{ $brand->id }}">
                                    {{ $brand->name }}
                                    <small class="text-muted">({{ $brand->products_count ?? 0 }})</small>
                                </label>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Nenhuma marca encontrada</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Resultados --}}
    <div class="row">
        <div class="col-12">
            {{-- Header dos resultados --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Produtos Encontrados</h4>
                    <p class="text-muted mb-0">
                        <span wire:loading.remove wire:target="searchBuffer,selectedCategories,selectedBrands,perPage">
                            {{ $productsCount }} {{ $productsCount == 1 ? 'produto encontrado' : 'produtos encontrados' }}
                            @if($hasActiveFilters)
                                <span class="badge bg-primary ms-2">
                                    <i class="fas fa-filter me-1"></i>
                                    Filtros ativos
                                </span>
                            @endif
                        </span>
                        <span wire:loading wire:target="searchBuffer,selectedCategories,selectedBrands,perPage">
                            <i class="fas fa-spinner fa-spin"></i> Buscando produtos...
                        </span>
                    </p>
                </div>
                
                <div class="d-flex gap-2 align-items-center">
                    <!-- Seletor de itens por página -->
                    <div class="d-flex align-items-center">
                        <label for="perPage" class="form-label me-2 mb-0 text-nowrap">Mostrar:</label>
                        <select 
                            wire:model.live="perPage" 
                            class="form-select form-select-sm" 
                            id="perPage"
                            style="width: auto;"
                        >
                            @foreach($perPageOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if($hasActiveFilters)
                        <button 
                            wire:click="clearFilters" 
                            class="btn btn-outline-secondary btn-sm"
                            title="Limpar todos os filtros"
                        >
                            <i class="fas fa-times me-1"></i>
                            Limpar Filtros
                        </button>
                    @endif
                    
                    <!-- Botão para compartilhar filtros -->
                    <button 
                        class="btn btn-outline-info btn-sm"
                        onclick="copyFilterUrl()"
                        title="Copiar link com filtros"
                    >
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>
            </div>

            {{-- Loading state --}}
            <div wire:loading class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2 text-muted">Aplicando filtros...</p>
            </div>

            {{-- Grid de produtos --}}
            <div wire:loading.remove>
                @if($products->count() > 0)
                    <div class="row g-4">
                        @foreach($products as $product)
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card h-100 shadow-sm product-card">
                                    {{-- Imagem do produto --}}
                                    <div class="position-relative">
                                        <img 
                                            src="{{ $product->image ?: 'https://via.placeholder.com/300x200?text=Produto' }}" 
                                            class="card-img-top" 
                                            alt="{{ $product->name }}"
                                            style="height: 200px; object-fit: cover;"
                                        >
                                        @if($product->stock <= 5)
                                            <span class="position-absolute top-0 end-0 badge bg-warning m-2">
                                                Estoque baixo
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Conteúdo do card --}}
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title text-truncate" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </h6>
                                        
                                        <p class="card-text text-muted small flex-grow-1">
                                            {{ Str::limit($product->description, 80) }}
                                        </p>
                                        
                                        {{-- Informações do produto --}}
                                        <div class="mb-2">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ $product->category->name }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-copyright me-1"></i>
                                                {{ $product->brand->name }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-barcode me-1"></i>
                                                SKU: {{ $product->sku }}
                                            </small>
                                        </div>
                                        
                                        {{-- Preço e estoque --}}
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <div>
                                                <span class="h5 text-primary mb-0">
                                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-boxes me-1"></i>
                                                    {{ $product->stock }} un.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Paginação --}}
                    <div class="d-flex justify-content-center mt-4">
                        <div class="pagination-wrapper">
                            {{ $products->links('custom.pagination') }}
                        </div>
                    </div>
                    
                    {{-- Informações sobre URL persistente --}}
                    @if($hasActiveFilters)
                    <div class="mt-3 p-3 bg-light rounded">
                        <p class="mb-1"><i class="fas fa-link me-2"></i><strong>Link permanente com filtros aplicados:</strong></p>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="filterUrl" value="{{ $filterUrl }}" readonly>
                            <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copyFilterUrl()">
                                <i class="fas fa-copy me-1"></i>Copiar
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block">Compartilhe este link para mostrar os mesmos resultados filtrados</small>
                    </div>
                    @endif
                @else
                    {{-- Nenhum produto encontrado --}}
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-search fa-3x text-muted"></i>
                        </div>
                        <h4 class="text-muted">Nenhum produto encontrado</h4>
                        <p class="text-muted mb-3">
                            @if($hasActiveFilters)
                                Tente ajustar os filtros para encontrar mais produtos.
                            @else
                                Não há produtos cadastrados no momento.
                            @endif
                        </p>
                        @if($hasActiveFilters)
                            <button 
                                wire:click="clearFilters" 
                                class="btn btn-primary"
                                type="button"
                            >
                                <i class="fas fa-times me-1"></i>
                                Limpar Filtros
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Scripts customizados --}}
<script>
    // Função para copiar link com filtros
    function copyFiltersLink() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentUrl = window.location.origin + window.location.pathname;
        
        // Se há parâmetros, incluir na URL
        if (urlParams.toString()) {
            const fullUrl = currentUrl + '?' + urlParams.toString();
            
            // Copiar para clipboard
            navigator.clipboard.writeText(fullUrl).then(() => {
                // Mostrar toast de sucesso
                const toast = document.createElement('div');
                toast.className = 'position-fixed bottom-0 end-0 p-3';
                toast.style.zIndex = '5';
                toast.innerHTML = `
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-success text-white">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong class="me-auto">Sucesso</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="this.parentElement.parentElement.parentElement.remove()"></button>
                        </div>
                        <div class="toast-body">
                            Link com filtros copiado para a área de transferência!
                        </div>
                    </div>
                `;
                document.body.appendChild(toast);
                
                // Remover toast após 3 segundos
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }).catch(() => {
                // Fallback para navegadores mais antigos
                const tempInput = document.createElement('input');
                tempInput.value = fullUrl;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                
                // Mostrar toast
                const toast = document.createElement('div');
                toast.className = 'position-fixed bottom-0 end-0 p-3';
                toast.style.zIndex = '5';
                toast.innerHTML = `
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-success text-white">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong class="me-auto">Sucesso</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="this.parentElement.parentElement.parentElement.remove()"></button>
                        </div>
                        <div class="toast-body">
                            Link com filtros copiado para a área de transferência!
                        </div>
                    </div>
                `;
                document.body.appendChild(toast);
                
                // Remover toast após 3 segundos
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            });
        } else {
            // Se não houver input (caso de nenhum filtro ativo), copiar URL atual
            const tempInput = document.createElement('input');
            tempInput.value = window.location.href;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            // Mostrar toast
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '5';
            toast.innerHTML = `
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success text-white">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong class="me-auto">Sucesso</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="this.parentElement.parentElement.parentElement.remove()"></button>
                    </div>
                    <div class="toast-body">
                        Link da página copiado para a área de transferência!
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            // Remover toast após 3 segundos
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    }
    
    // Inicializar listeners para eventos Livewire
    document.addEventListener('livewire:initialized', () => {
        // Atualizar URL no histórico quando filtros mudarem
        Livewire.on('filtersApplied', () => {
            // Pequeno delay para garantir que a URL foi atualizada
            setTimeout(() => {
                // Atualizar título da página com contagem
                const count = document.querySelector('[wire\\:id]').__livewire.$wire.productsCount;
                document.title = `${count} Produtos Encontrados - Filtros de Produtos`;
            }, 100);
        });
        
        // Limpar URL quando filtros forem resetados
        Livewire.on('filtersCleared', () => {
            document.title = 'Todos os Produtos - Filtros de Produtos';
        });
    });
</script>
@endpush

@push('styles')
{{-- Estilos customizados --}}
<style>
.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.badge {
    font-size: 0.75em;
}

[wire\:loading] {
    opacity: 0.7;
}

/* Estilos customizados para paginação */
.pagination-wrapper {
    width: 100%;
}

.pagination-info {
    font-size: 0.875rem;
    color: #6c757d;
}

.per-page-selector .form-select {
    min-width: 140px;
    font-size: 0.875rem;
}

.pagination-custom {
    --bs-pagination-padding-x: 0.75rem;
    --bs-pagination-padding-y: 0.5rem;
    --bs-pagination-font-size: 0.875rem;
    --bs-pagination-color: #0d6efd;
    --bs-pagination-bg: #fff;
    --bs-pagination-border-width: 1px;
    --bs-pagination-border-color: #dee2e6;
    --bs-pagination-border-radius: 0.375rem;
    --bs-pagination-hover-color: #0a58ca;
    --bs-pagination-hover-bg: #e9ecef;
    --bs-pagination-hover-border-color: #dee2e6;
    --bs-pagination-focus-color: #0a58ca;
    --bs-pagination-focus-bg: #e9ecef;
    --bs-pagination-focus-box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    --bs-pagination-active-color: #fff;
    --bs-pagination-active-bg: #0d6efd;
    --bs-pagination-active-border-color: #0d6efd;
    --bs-pagination-disabled-color: #6c757d;
    --bs-pagination-disabled-bg: #fff;
    --bs-pagination-disabled-border-color: #dee2e6;
}

.pagination-custom .page-link {
    border: none;
    margin: 0 2px;
    border-radius: 0.375rem !important;
    transition: all 0.2s ease-in-out;
    font-weight: 500;
}

.pagination-custom .page-item.active .page-link {
    background-color: var(--bs-pagination-active-bg);
    border-color: var(--bs-pagination-active-border-color);
    color: var(--bs-pagination-active-color);
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
}

.pagination-custom .page-link:hover {
    background-color: var(--bs-pagination-hover-bg);
    border-color: var(--bs-pagination-hover-border-color);
    color: var(--bs-pagination-hover-color);
    transform: translateY(-1px);
}

.pagination-custom .page-item.disabled .page-link {
    background-color: var(--bs-pagination-disabled-bg);
    border-color: var(--bs-pagination-disabled-border-color);
    color: var(--bs-pagination-disabled-color);
    opacity: 0.6;
}

.pagination-custom .page-link i {
    font-size: 0.75rem;
}

/* Responsividade para paginação */
@media (max-width: 576px) {
    .pagination-info {
        font-size: 0.75rem;
        text-align: center;
        margin-bottom: 0.5rem;
    }
    
    .per-page-selector {
        text-align: center;
    }
    
    .per-page-selector .form-select {
        min-width: 120px;
        font-size: 0.75rem;
    }
    
    .pagination-custom {
        --bs-pagination-padding-x: 0.5rem;
        --bs-pagination-padding-y: 0.375rem;
        --bs-pagination-font-size: 0.75rem;
    }
    
    .pagination-custom .page-link {
        margin: 0 1px;
    }
}
</style>
@endpush

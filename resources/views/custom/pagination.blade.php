@if ($paginator->hasPages())
    <nav aria-label="Navegação de páginas">
        <div class="d-flex justify-content-between align-items-center mb-3">
            {{-- Informações sobre resultados --}}
            <div class="pagination-info">
                <span class="text-muted small">
                    Mostrando {{ $paginator->firstItem() }} até {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
                </span>
            </div>
            
            {{-- Seletor de itens por página --}}
            <div class="per-page-selector">
                <select wire:model.live="perPage" class="form-select form-select-sm" style="width: auto;">
                    <option value="6">6 por página</option>
                    <option value="12">12 por página</option>
                    <option value="24">24 por página</option>
                    <option value="48">48 por página</option>
                </select>
            </div>
        </div>
        
        <ul class="pagination pagination-custom justify-content-center mb-0">
            {{-- Link para primeira página --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="fas fa-angle-double-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <button type="button" class="page-link" wire:click="gotoPage(1)" rel="first">
                        <i class="fas fa-angle-double-left"></i>
                    </button>
                </li>
            @endif

            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="fas fa-angle-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <button type="button" class="page-link" wire:click="previousPage" rel="prev">
                        <i class="fas fa-angle-left"></i>
                    </button>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <button type="button" class="page-link" wire:click="gotoPage({{ $page }})">
                                    {{ $page }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button type="button" class="page-link" wire:click="nextPage" rel="next">
                        <i class="fas fa-angle-right"></i>
                    </button>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </li>
            @endif

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button type="button" class="page-link" wire:click="gotoPage({{ $paginator->lastPage() }})" rel="last">
                        <i class="fas fa-angle-double-right"></i>
                    </button>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="fas fa-angle-double-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
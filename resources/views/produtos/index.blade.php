@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i>
                    Início
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-box me-1"></i>
                Produtos
            </li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fas fa-search me-2"></i>
                Catálogo de Produtos
            </h2>
            <p class="text-muted mb-0">Encontre produtos usando os filtros abaixo</p>
        </div>
    </div>

    @livewire('product-filter')
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('filtersCleared', () => {
            showToast('Filtros limpos com sucesso!', 'success');
        });
        
        Livewire.on('filtersApplied', (data) => {
            const count = data[0]?.count || 0;
            showToast(`${count} produto(s) encontrado(s)`, 'info');
        });
    });
    
    function copyProductLink(productId) {
        const url = `${window.location.origin}/produtos/${productId}`;
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link do produto copiado!', 'success');
        }).catch(() => {
            showToast('Erro ao copiar link', 'danger');
        });
    }
    
    function toggleFavorite(productId) {
        showToast('Produto adicionado aos favoritos!', 'success');
    }
</script>
@endpush
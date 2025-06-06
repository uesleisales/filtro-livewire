@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Produtos</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 text-gray-800 mb-2 d-flex align-items-center">
                    <i class="fas fa-box text-primary me-3"></i>
                    Produtos
                </h1>
                <p class="text-muted mb-0">Gerencie e visualize todos os produtos do sistema com filtros avançados</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex align-items-center justify-content-end">
                    <span class="badge bg-primary me-2">
                        <i class="fas fa-filter me-1"></i>
                        Sistema de Filtros
                    </span>
                </div>
            </div>
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
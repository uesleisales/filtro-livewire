@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb --}}
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

    {{-- Componente Livewire de Filtros --}}
    @livewire('product-filter')
</div>
@endsection

@push('scripts')
<script>
    // Scripts específicos da página de produtos
    document.addEventListener('livewire:init', () => {
        // Listener para eventos do componente ProductFilter
        Livewire.on('filtersCleared', () => {
            showToast('Filtros limpos com sucesso!', 'success');
        });
        
        // Listener para mudanças nos filtros
        Livewire.on('filtersApplied', (data) => {
            const count = data[0]?.count || 0;
            showToast(`${count} produto(s) encontrado(s)`, 'info');
        });
    });
    
    // Função para copiar link do produto
    function copyProductLink(productId) {
        const url = `${window.location.origin}/produtos/${productId}`;
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link do produto copiado!', 'success');
        }).catch(() => {
            showToast('Erro ao copiar link', 'danger');
        });
    }
    
    // Função para favoritar produto (simulação)
    function toggleFavorite(productId) {
        // Aqui seria implementada a lógica real de favoritos
        showToast('Produto adicionado aos favoritos!', 'success');
    }
</script>
@endpush
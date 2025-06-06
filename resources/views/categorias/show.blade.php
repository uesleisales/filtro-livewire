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
            <li class="breadcrumb-item">
                <a href="{{ route('categories.index') }}" class="text-decoration-none">
                    <i class="fas fa-tags me-1"></i>
                    Categorias
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-eye me-1"></i>
                {{ $categoria->name }}
            </li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fas fa-eye me-2"></i>
                Detalhes da Categoria
            </h2>
            <p class="text-muted mb-0">Visualize as informações da categoria</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('categories.edit', $categoria) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>
                Editar
            </a>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações da Categoria
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong><i class="fas fa-hashtag me-1"></i> ID:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $categoria->id }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong><i class="fas fa-tag me-1"></i> Nome:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $categoria->name }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong><i class="fas fa-align-left me-1"></i> Descrição:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $categoria->description ?? 'Não informada' }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong><i class="fas fa-calendar-plus me-1"></i> Criado em:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $categoria->created_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong><i class="fas fa-calendar-edit me-1"></i> Atualizado em:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $categoria->updated_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box me-2"></i>
                        Produtos da Categoria
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-primary mb-2">
                            <i class="fas fa-cubes me-2"></i>
                            {{ $categoria->products->count() }}
                        </h3>
                        <p class="text-muted mb-0">produtos cadastrados</p>
                    </div>
                    
                    @if($categoria->products->count() > 0)
                        <hr>
                        <div class="mt-3">
                            <h6 class="mb-2">Últimos produtos:</h6>
                            @foreach($categoria->products->take(5) as $produto)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-truncate me-2">{{ $produto->name }}</small>
                                    <small class="text-muted">R$ {{ number_format($produto->price, 2, ',', '.') }}</small>
                                </div>
                            @endforeach
                            
                            @if($categoria->products->count() > 5)
                                <small class="text-muted">e mais {{ $categoria->products->count() - 5 }} produtos...</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
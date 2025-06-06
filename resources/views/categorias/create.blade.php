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
                <i class="fas fa-plus me-1"></i>
                Nova Categoria
            </li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>
                Nova Categoria
            </h2>
            <p class="text-muted mb-0">Preencha os dados para criar uma nova categoria</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Dados da Categoria
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag me-1"></i>
                                Nome da Categoria <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Digite o nome da categoria"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>
                                Descrição
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Digite uma descrição para a categoria (opcional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                A descrição é opcional e pode ajudar a identificar melhor a categoria.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Salvar Categoria
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('name').focus();
        
        const nameInput = document.getElementById('name');
        nameInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });
    });
</script>
@endpush
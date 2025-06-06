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
                <i class="fas fa-copyright me-1"></i>
                Marcas
            </li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fas fa-copyright me-2"></i>
                Marcas
            </h2>
            <p class="text-muted mb-0">Gerencie as marcas dos produtos</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('brands.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Nova Marca
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>
                Lista de Marcas
            </h5>
        </div>
        <div class="card-body p-0">
            @if($marcas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Descrição</th>
                                <th scope="col">Produtos</th>
                                <th scope="col">Criado em</th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($marcas as $marca)
                            <tr>
                                <td>{{ $marca->id }}</td>
                                        <td>{{ $marca->name }}</td>
                                        <td>{{ $marca->description ?? '-' }}</td>
                                        <td>{{ $marca->products_count }}</td>
                                        <td>{{ $marca->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('brands.show', $marca) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                        <a href="{{ route('brands.edit', $marca) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </a>
                                        <form action="{{ route('brands.destroy', $marca) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta marca?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhuma marca encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginação --}}
                @if($marcas->hasPages())
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Mostrando {{ $marcas->firstItem() }} a {{ $marcas->lastItem() }} 
                                    de {{ $marcas->total() }} marcas
                                </small>
                            </div>
                            <div>
                                {{ $marcas->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-copyright fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhuma marca encontrada</h4>
                    <p class="text-muted mb-4">Comece criando sua primeira marca de produtos.</p>
                    <a href="{{ route('brands.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Criar Primeira Marca
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>
@endpush
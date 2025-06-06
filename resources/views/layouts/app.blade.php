<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Sistema de Filtros</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a67d8;
            --secondary-color: #718096;
            --success-color: #48bb78;
            --info-color: #4299e1;
            --warning-color: #ed8936;
            --danger-color: #f56565;
            --light-color: #f7fafc;
            --dark-color: #2d3748;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            line-height: 1.6;
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .main-content {
            min-height: calc(100vh - 140px);
            padding: 2rem 0;
        }

        .footer {
            background: var(--gradient-primary);
            color: white;
            padding: 2.5rem 0;
            margin-top: auto;
            box-shadow: var(--shadow-lg);
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        /* Animações customizadas */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Responsividade melhorada */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .btn-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }
        }

        /* Melhorias para acessibilidade */
        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Estilo para elementos Livewire */
        [wire\:loading] {
            opacity: 0.6;
            pointer-events: none;
        }

        [wire\:loading\.delay] {
            opacity: 1;
        }

        [wire\:loading\.delay\.shortest] {
            opacity: 1;
        }

        [wire\:loading\.delay\.shorter] {
            opacity: 1;
        }

        [wire\:loading\.delay\.short] {
            opacity: 1;
        }

        [wire\:loading\.delay\.long] {
            opacity: 0.6;
        }

        [wire\:loading\.delay\.longer] {
            opacity: 0.6;
        }

        [wire\:loading\.delay\.longest] {
            opacity: 0.6;
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-filter me-2"></i>
                {{ config('app.name', 'Laravel') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="fas fa-home me-1"></i>
                            Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/produtos') }}">
                            <i class="fas fa-box me-1"></i>
                            Produtos
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog me-1"></i>
                            Sistema
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('categories.index') }}"><i class="fas fa-tags me-2"></i>Categories</a></li>
                            <li><a class="dropdown-item" href="{{ route('brands.index') }}"><i class="fas fa-copyright me-2"></i>Brands</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chart-bar me-2"></i>Relatórios</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">
                        <i class="fas fa-code me-1"></i>
                        Sistema de Filtros Laravel + Livewire
                    </p>
                    <small class="text-muted">
                        Desenvolvido com <i class="fas fa-heart text-danger"></i> usando Laravel {{ app()->version() }}
                    </small>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="fas fa-calendar me-1"></i>
                        © {{ date('Y') }} - Todos os direitos reservados
                    </p>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Última atualização: {{ date('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Melhorar UX com loading states
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updating', ({ el, component, cleanup }) => {
                // Adicionar classe de loading
                el.classList.add('updating');
                
                cleanup(() => {
                    // Remover classe de loading
                    el.classList.remove('updating');
                });
            });
        });

        // Smooth scroll para elementos
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar smooth scroll para links internos
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Adicionar animação fade-in para elementos
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, observerOptions);

            // Observar cards de produtos
            document.querySelectorAll('.product-card').forEach(card => {
                observer.observe(card);
            });
        });

        // Função para mostrar toast notifications
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            const toast = createToast(message, type);
            toastContainer.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }

        function createToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            return toast;
        }

        // Expor função globalmente para uso com Livewire
        window.showToast = showToast;
    </script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
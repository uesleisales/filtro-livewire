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
            border-radius: 1rem;
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card-header {
            background: var(--gradient-primary) !important;
            color: white;
            border-radius: 1rem 1rem 0 0 !important;
            border: none;
            padding: 1.5rem;
        }

        .btn {
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--gradient-primary);
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            background: var(--gradient-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }

        .form-control, .form-select {
            border-radius: 0.75rem;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
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

        /* Navbar Improvements */
        .navbar {
            z-index: 1050;
        }

        .navbar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .dropdown-menu {
            z-index: 1060;
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: 0.75rem;
            margin-top: 0.5rem;
        }

        .dropdown-item:hover {
            background: var(--gradient-primary) !important;
            color: white !important;
            transform: translateX(5px);
        }

        .dropdown-item:hover i {
            color: white !important;
        }

        /* Breadcrumb Improvements */
        .breadcrumb {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1040;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "→";
            color: var(--primary-color);
            font-weight: bold;
        }

        /* Page Headers */
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(10px);
        }

        /* Form Check Improvements */
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check:hover .form-check-label {
            color: var(--primary-color);
            transform: translateX(3px);
        }

        /* Badge Improvements */
        .badge {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.5rem 0.75rem;
        }

        /* Loading States */
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Filter Section Improvements */
        .filter-section {
            height: 100%;
        }

        .hover-bg-white:hover {
            background-color: rgba(255, 255, 255, 0.8) !important;
            transform: translateX(2px);
            transition: all 0.2s ease;
        }

        .bg-gradient {
            background: var(--gradient-primary) !important;
        }

        .form-check-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
        }

        .input-group-text {
            border-color: #dee2e6;
        }

        .border-start-0 {
            border-left: 0 !important;
        }

        .border-end-0 {
            border-right: 0 !important;
        }

        /* Product Cards */
        .product-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: var(--shadow-sm);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        /* Toast Improvements */
        .toast {
            border: none;
            box-shadow: var(--shadow-lg);
        }

        .toast-header {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .navbar-brand .bg-white {
                width: 35px;
                height: 35px;
            }
            
            .page-header {
                padding: 1.5rem;
            }

            .filter-section {
                margin-bottom: 2rem;
            }

            .btn-lg {
                font-size: 1rem;
                padding: 0.75rem 1.5rem;
            }
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-lg" style="background: var(--gradient-primary); backdrop-filter: blur(10px);">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <div class="bg-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-filter text-primary"></i>
                </div>
                <div>
                    <div class="fw-bold">{{ config('app.name', 'Laravel') }}</div>
                    <small class="opacity-75">Sistema de Filtros</small>
                </div>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded-pill mx-1 transition-all" href="{{ url('/') }}" style="transition: all 0.3s ease;">
                            <i class="fas fa-home me-2"></i>
                            Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded-pill mx-1" href="{{ url('/produtos') }}" style="transition: all 0.3s ease;">
                            <i class="fas fa-box me-2"></i>
                            Produtos
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3 py-2 rounded-pill mx-1" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="transition: all 0.3s ease;">
                            <i class="fas fa-cog me-2"></i>
                            Sistema
                        </a>
                        <ul class="dropdown-menu border-0 shadow-lg" style="border-radius: 1rem; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95);" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item py-2 px-3 rounded-3 mx-2 my-1" href="{{ route('categories.index') }}" style="transition: all 0.3s ease;"><i class="fas fa-tags me-2 text-primary"></i>Categorias</a></li>
                            <li><a class="dropdown-item py-2 px-3 rounded-3 mx-2 my-1" href="{{ route('brands.index') }}" style="transition: all 0.3s ease;"><i class="fas fa-copyright me-2 text-primary"></i>Marcas</a></li>
                            <li><hr class="dropdown-divider mx-2"></li>
                            <li><a class="dropdown-item py-2 px-3 rounded-3 mx-2 my-1" href="#" style="transition: all 0.3s ease;"><i class="fas fa-chart-bar me-2 text-primary"></i>Relatórios</a></li>
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
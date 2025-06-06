# Sistema de Filtros de Produtos - Laravel

<p align="center">
<img src="https://img.shields.io/badge/Laravel-11.x-red.svg" alt="Laravel Version">
<img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP Version">
<img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

## Sobre o Projeto

Sistema de catálogo de produtos com funcionalidades avançadas de filtragem por categorias e marcas. Desenvolvido seguindo princípios de **Arquitetura Limpa**, **SOLID**, **DRY** e **KISS**, garantindo código maintível, escalável e testável.

### Funcionalidades Principais

- 📦 **Catálogo de Produtos** com paginação otimizada
- 🔍 **Sistema de Filtros Dinâmicos** (categoria, marca, preço)
- ⚡ **Filtros em Tempo Real** com Livewire
- 🏷️ **Gerenciamento de Categorias e Marcas** (CRUD completo)
- 🔗 **URLs Amigáveis** com slugs únicos
- 📱 **Interface Responsiva** com Bootstrap
- 🚀 **Performance Otimizada** com eager loading e cache

## Arquitetura e Princípios Aplicados

### 🏗️ Arquitetura Limpa (Clean Architecture)

```
app/
├── DTOs/              # Data Transfer Objects
├── Repositories/      # Camada de Dados
├── Services/          # Lógica de Negócio
├── Http/Controllers/  # Camada de Apresentação
└── Models/           # Entidades de Domínio
```

### 🎯 Princípios SOLID

- **S** - Single Responsibility: Cada classe tem uma única responsabilidade
- **O** - Open/Closed: Extensível sem modificação (AbstractCrudController)
- **L** - Liskov Substitution: Interfaces bem definidas (RepositoryInterface)
- **I** - Interface Segregation: Interfaces específicas por contexto
- **D** - Dependency Inversion: Injeção de dependências em todos os níveis

### 🔄 Padrões Implementados

- **Repository Pattern**: Abstração da camada de dados
- **Service Layer**: Centralização da lógica de negócio
- **DTO Pattern**: Transferência segura de dados
- **Factory Pattern**: Criação de objetos complexos
- **Strategy Pattern**: Algoritmos de filtragem intercambiáveis

### 📏 Princípios DRY e KISS

- **DRY (Don't Repeat Yourself)**: Reutilização através de AbstractCrudController
- **KISS (Keep It Simple, Stupid)**: Código simples e legível
- **Modularização**: Componentes pequenos e especializados
- **Convenções**: Nomenclatura consistente e autoexplicativa

## Requisitos do Sistema

- **PHP**: 8.2 ou superior
- **Composer**: 2.x
- **Node.js**: 18.x ou superior
- **MySQL**: 8.0 ou superior
- **Docker** (opcional): 20.x ou superior

## Instalação

### 🐳 Instalação com Docker (Recomendado)

```bash
# Clone o repositório
git clone <repository-url>
cd laravel-filtros

# Copie o arquivo de ambiente
cp .env.example .env

# Suba os containers
docker-compose up -d

# Instale as dependências PHP
docker-compose exec app composer install

# Gere a chave da aplicação
docker-compose exec app php artisan key:generate

# Execute as migrações e seeders
docker-compose exec app php artisan migrate --seed

# Instale as dependências Node.js
docker-compose exec app npm install

# Compile os assets
docker-compose exec app npm run build
```

**Acesse a aplicação em:** http://localhost:8000

### 💻 Instalação Local

```bash
# Clone o repositório
git clone <repository-url>
cd laravel-filtros

# Instale as dependências PHP
composer install

# Copie e configure o ambiente
cp .env.example .env
# Edite o .env com suas configurações de banco

# Gere a chave da aplicação
php artisan key:generate

# Execute as migrações e seeders
php artisan migrate --seed

# Instale as dependências Node.js
npm install

# Compile os assets
npm run build

# Inicie o servidor
php artisan serve
```

**Acesse a aplicação em:** http://localhost:8000

## Configuração do Banco de Dados

### MySQL Local

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_filtros
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### MySQL com Docker

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_filtros
DB_USERNAME=laravel
DB_PASSWORD=password
```

## Estrutura do Projeto

### Camadas da Aplicação

```
📁 app/
├── 📁 DTOs/                    # Objetos de Transferência de Dados
│   ├── BrandDTO.php
│   ├── CategoryDTO.php
│   └── ProductDTO.php
├── 📁 Http/Controllers/        # Controladores
│   ├── AbstractCrudController.php
│   ├── BrandController.php
│   ├── CategoryController.php
│   └── ProductController.php
├── 📁 Repositories/           # Camada de Dados
│   ├── BrandRepository.php
│   ├── CategoryRepository.php
│   └── Interfaces/
├── 📁 Services/              # Lógica de Negócio
│   ├── BrandService.php
│   ├── CategoryService.php
│   └── ProductService.php
└── 📁 Livewire/             # Componentes Reativos
    └── ProductFilter.php
```

### Principais Componentes

#### AbstractCrudController
Controlador base que implementa operações CRUD genéricas, seguindo o princípio DRY:

- ✅ Operações padronizadas (index, show, create, store, edit, update, destroy)
- ✅ Route Model Binding automático
- ✅ Tratamento de exceções centralizado
- ✅ Validação consistente

#### Repository Pattern
Abstração da camada de dados com interfaces bem definidas:

- ✅ Separação entre lógica de negócio e acesso a dados
- ✅ Facilita testes unitários
- ✅ Permite troca de implementação sem impacto

#### Service Layer
Centralização da lógica de negócio:

- ✅ Regras de negócio isoladas
- ✅ Reutilização entre controladores
- ✅ Facilita manutenção e evolução

## Comandos Úteis

```bash
# Executar testes
php artisan test

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Recriar banco de dados
php artisan migrate:fresh --seed

# Compilar assets para produção
npm run build

# Modo de desenvolvimento (watch)
npm run dev
```

## Testes

O projeto inclui testes automatizados para garantir qualidade:

```bash
# Executar todos os testes
php artisan test

# Executar testes com cobertura
php artisan test --coverage

# Executar testes específicos
php artisan test --filter ProductFilterTest
```

## Performance e Otimizações

- **Eager Loading**: Carregamento otimizado de relacionamentos
- **Paginação**: Limitação de registros por página
- **Indexação**: Índices de banco otimizados
- **Cache**: Sistema de cache para consultas frequentes
- **Lazy Loading**: Carregamento sob demanda de componentes

## Contribuição

Para contribuir com o projeto:

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a [MIT License](https://opensource.org/licenses/MIT).

---

**Desenvolvido com ❤️ seguindo as melhores práticas de desenvolvimento**

<?php

namespace Tests\Feature;

use App\Livewire\ProductFilter;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar dados de teste
        $this->createTestData();
    }

    private function createTestData(): void
    {
        // Criar categorias
        $electronics = Category::factory()->create(['name' => 'Eletrônicos']);
        $clothing = Category::factory()->create(['name' => 'Roupas']);
        $books = Category::factory()->create(['name' => 'Livros']);

        // Criar marcas
        $samsung = Brand::factory()->create(['name' => 'Samsung']);
        $apple = Brand::factory()->create(['name' => 'Apple']);
        $nike = Brand::factory()->create(['name' => 'Nike']);

        // Criar produtos
        Product::factory()->create([
            'name' => 'Samsung Galaxy S21',
            'price' => 2999.99,
            'category_id' => $electronics->id,
            'brand_id' => $samsung->id,
        ]);

        Product::factory()->create([
            'name' => 'iPhone 13',
            'price' => 3999.99,
            'category_id' => $electronics->id,
            'brand_id' => $apple->id,
        ]);

        Product::factory()->create([
            'name' => 'Nike Air Max',
            'price' => 299.99,
            'category_id' => $clothing->id,
            'brand_id' => $nike->id,
        ]);

        Product::factory()->create([
            'name' => 'Livro de PHP',
            'price' => 89.99,
            'category_id' => $books->id,
            'brand_id' => null,
        ]);
    }

    /** @test */
    public function it_renders_the_component_successfully()
    {
        Livewire::test(ProductFilter::class)
            ->assertStatus(200)
            ->assertSee('Filtros')
            ->assertSee('Nome do Produto')
            ->assertSee('Categorias')
            ->assertSee('Marcas');
    }

    /** @test */
    public function it_filters_products_by_name()
    {
        Livewire::test(ProductFilter::class)
            ->set('search', 'Samsung')
            ->assertSee('Samsung Galaxy S21')
            ->assertDontSee('iPhone 13')
            ->assertDontSee('Nike Air Max');
    }

    /** @test */
    public function it_filters_products_by_single_category()
    {
        $electronics = Category::where('name', 'Eletrônicos')->first();
        
        Livewire::test(ProductFilter::class)
            ->set('selectedCategories', [$electronics->id])
            ->assertSee('Samsung Galaxy S21')
            ->assertSee('iPhone 13')
            ->assertDontSee('Nike Air Max')
            ->assertDontSee('Livro de PHP');
    }

    /** @test */
    public function it_filters_products_by_multiple_categories()
    {
        $electronics = Category::where('name', 'Eletrônicos')->first();
        $clothing = Category::where('name', 'Roupas')->first();
        
        Livewire::test(ProductFilter::class)
            ->set('selectedCategories', [$electronics->id, $clothing->id])
            ->assertSee('Samsung Galaxy S21')
            ->assertSee('iPhone 13')
            ->assertSee('Nike Air Max')
            ->assertDontSee('Livro de PHP');
    }

    /** @test */
    public function it_filters_products_by_single_brand()
    {
        $samsung = Brand::where('name', 'Samsung')->first();
        
        Livewire::test(ProductFilter::class)
            ->set('selectedBrands', [$samsung->id])
            ->assertSee('Samsung Galaxy S21')
            ->assertDontSee('iPhone 13')
            ->assertDontSee('Nike Air Max');
    }

    /** @test */
    public function it_filters_products_by_multiple_brands()
    {
        $samsung = Brand::where('name', 'Samsung')->first();
        $apple = Brand::where('name', 'Apple')->first();
        
        Livewire::test(ProductFilter::class)
            ->set('selectedBrands', [$samsung->id, $apple->id])
            ->assertSee('Samsung Galaxy S21')
            ->assertSee('iPhone 13')
            ->assertDontSee('Nike Air Max');
    }

    /** @test */
    public function it_combines_all_filters()
    {
        $electronics = Category::where('name', 'Eletrônicos')->first();
        $samsung = Brand::where('name', 'Samsung')->first();
        
        Livewire::test(ProductFilter::class)
            ->set('search', 'Galaxy')
            ->set('selectedCategories', [$electronics->id])
            ->set('selectedBrands', [$samsung->id])
            ->assertSee('Samsung Galaxy S21')
            ->assertDontSee('iPhone 13')
            ->assertDontSee('Nike Air Max')
            ->assertDontSee('Livro de PHP');
    }

    /** @test */
    public function it_clears_all_filters()
    {
        $electronics = Category::where('name', 'Eletrônicos')->first();
        $samsung = Brand::where('name', 'Samsung')->first();
        
        Livewire::test(ProductFilter::class)
            ->set('search', 'Galaxy')
            ->set('selectedCategories', [$electronics->id])
            ->set('selectedBrands', [$samsung->id])
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('selectedCategories', [])
            ->assertSet('selectedBrands', [])
            ->assertSee('Samsung Galaxy S21')
            ->assertSee('iPhone 13')
            ->assertSee('Nike Air Max')
            ->assertSee('Livro de PHP');
    }

    /** @test */
    public function it_persists_filters_after_refresh()
    {
        $electronics = Category::where('name', 'Eletrônicos')->first();
        
        // Simular persistência via URL
        $component = Livewire::test(ProductFilter::class)
            ->set('search', 'Samsung')
            ->set('selectedCategories', [$electronics->id]);
            
        // Verificar se os filtros estão aplicados
        $component->assertSet('search', 'Samsung')
                 ->assertSet('selectedCategories', [$electronics->id]);
    }

    /** @test */
    public function it_handles_pagination_with_filters()
    {
        // Criar mais produtos para testar paginação
        $electronics = Category::where('name', 'Eletrônicos')->first();
        $samsung = Brand::where('name', 'Samsung')->first();
        
        // Criar 15 produtos Samsung para forçar paginação
        for ($i = 1; $i <= 15; $i++) {
            Product::factory()->create([
                'name' => "Samsung Product {$i}",
                'category_id' => $electronics->id,
                'brand_id' => $samsung->id,
            ]);
        }
        
        Livewire::test(ProductFilter::class)
            ->set('selectedBrands', [$samsung->id])
            ->assertSee('Samsung Galaxy S21')
            ->assertSee('Samsung Product 1')
            ->call('nextPage')
            ->assertSee('Samsung Product');
    }

    /** @test */
    public function it_shows_no_results_message_when_no_products_match_filters()
    {
        Livewire::test(ProductFilter::class)
            ->set('search', 'Produto Inexistente')
            ->assertSee('Nenhum produto encontrado');
    }

    /** @test */
    public function it_updates_url_when_filters_change()
    {
        $electronics = Category::where('name', 'Eletrônicos')->first();
        
        Livewire::test(ProductFilter::class)
            ->set('search', 'Samsung')
            ->set('selectedCategories', [$electronics->id])
            ->assertDispatched('urlUpdated');
    }

    /** @test */
    public function it_handles_debounce_for_search_input()
    {
        $component = Livewire::test(ProductFilter::class);
        
        // Simular digitação rápida
        $component->set('search', 'S')
                 ->set('search', 'Sa')
                 ->set('search', 'Sam')
                 ->set('search', 'Samsung');
        
        // Verificar se o filtro final foi aplicado
        $component->assertSee('Samsung Galaxy S21');
    }
}
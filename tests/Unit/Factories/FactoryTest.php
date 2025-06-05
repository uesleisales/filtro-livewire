<?php

namespace Tests\Unit\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function product_factory_creates_valid_product()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertNotNull($product->name);
        $this->assertNotNull($product->price);
        $this->assertNotNull($product->category_id);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
        ]);
    }

    /** @test */
    public function product_factory_can_create_with_specific_attributes()
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();
        
        $product = Product::factory()->create([
            'name' => 'Custom Product',
            'price' => 99.99,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(99.99, $product->price);
        $this->assertEquals($category->id, $product->category_id);
        $this->assertEquals($brand->id, $product->brand_id);
    }

    /** @test */
    public function product_factory_can_create_without_brand()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'brand_id' => null,
            'category_id' => $category->id
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertNull($product->brand_id);
        $this->assertNull($product->brand);
    }

    /** @test */
    public function product_factory_creates_multiple_products()
    {
        $products = Product::factory()->count(5)->create();

        $this->assertCount(5, $products);
        $this->assertEquals(5, Product::count());
        
        foreach ($products as $product) {
            $this->assertNotNull($product->name);
            $this->assertNotNull($product->price);
            $this->assertNotNull($product->category_id);
        }
    }

    /** @test */
    public function category_factory_creates_valid_category()
    {
        $category = Category::factory()->create();

        $this->assertNotNull($category->name);
        $this->assertIsString($category->name);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    /** @test */
    public function category_factory_can_create_with_specific_attributes()
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'description' => 'Test Description',
        ]);

        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('Test Description', $category->description);
    }

    /** @test */
    public function category_factory_creates_multiple_categories()
    {
        $categories = Category::factory()->count(3)->create();

        $this->assertCount(3, $categories);
        $this->assertEquals(3, Category::count());
        
        foreach ($categories as $category) {
            $this->assertNotNull($category->name);
        }
    }

    /** @test */
    public function brand_factory_creates_valid_brand()
    {
        $brand = Brand::factory()->create();

        $this->assertNotNull($brand->name);
        $this->assertIsString($brand->name);
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => $brand->name,
        ]);
    }

    /** @test */
    public function brand_factory_can_create_with_specific_attributes()
    {
        $brand = Brand::factory()->create([
            'name' => 'Test Brand',
            'description' => 'Test Brand Description',
        ]);

        $this->assertEquals('Test Brand', $brand->name);
        $this->assertEquals('Test Brand Description', $brand->description);
    }

    /** @test */
    public function brand_factory_creates_multiple_brands()
    {
        $brands = Brand::factory()->count(4)->create();

        $this->assertCount(4, $brands);
        $this->assertEquals(4, Brand::count());
        
        foreach ($brands as $brand) {
            $this->assertNotNull($brand->name);
        }
    }

    /** @test */
    public function factories_work_together_for_complete_product()
    {
        $category = Category::factory()->create(['name' => 'Electronics']);
        $brand = Brand::factory()->create(['name' => 'Samsung']);
        
        $product = Product::factory()->create([
            'name' => 'Samsung Galaxy S21',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ]);

        $this->assertEquals('Samsung Galaxy S21', $product->name);
        $this->assertEquals('Electronics', $product->category->name);
        $this->assertEquals('Samsung', $product->brand->name);
    }

    /** @test */
    public function product_factory_generates_realistic_prices()
    {
        $category = Category::factory()->create();
        $products = Product::factory()->count(10)->create(['category_id' => $category->id]);

        foreach ($products as $product) {
            $this->assertGreaterThan(0, $product->price);
            $this->assertLessThan(10000, $product->price); // Assumindo preço máximo razoável
            $this->assertIsFloat($product->price);
        }
    }

    /** @test */
    public function factories_create_unique_names_when_possible()
    {
        $categories = Category::factory()->count(5)->create();
        $brands = Brand::factory()->count(5)->create();
        $products = Product::factory()->count(10)->create(['category_id' => $categories->first()->id]);

        $categoryNames = $categories->pluck('name')->toArray();
        $brandNames = $brands->pluck('name')->toArray();
        $productNames = $products->pluck('name')->toArray();

        // Verificar se há variedade nos nomes (não necessariamente únicos, mas variados)
        $this->assertGreaterThan(1, count(array_unique($categoryNames)));
        $this->assertGreaterThan(1, count(array_unique($brandNames)));
        $this->assertGreaterThan(1, count(array_unique($productNames)));
    }

    /** @test */
    public function product_factory_can_create_with_states()
    {
        // Teste para estados específicos da factory, se implementados
        $category = Category::factory()->create();
        $expensiveProduct = Product::factory()->create([
            'price' => 5000.00,
            'category_id' => $category->id
        ]);
        $cheapProduct = Product::factory()->create([
            'price' => 10.00,
            'category_id' => $category->id
        ]);

        $this->assertEquals(5000.00, $expensiveProduct->price);
        $this->assertEquals(10.00, $cheapProduct->price);
    }
}
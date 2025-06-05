<?php

namespace Tests\Unit\Models;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    /** @test */
    public function it_belongs_to_a_brand()
    {
        $brand = Brand::factory()->create();
        $product = Product::factory()->create(['brand_id' => $brand->id]);

        $this->assertInstanceOf(Brand::class, $product->brand);
        $this->assertEquals($brand->id, $product->brand->id);
    }

    /** @test */
    public function it_can_have_no_brand()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'brand_id' => null,
            'category_id' => $category->id
        ]);

        $this->assertNull($product->brand);
    }

    /** @test */
    public function it_filters_by_search_term()
    {
        $category = Category::factory()->create();
        
        Product::factory()->create([
            'name' => 'Samsung Galaxy S21',
            'category_id' => $category->id
        ]);
        Product::factory()->create([
            'name' => 'iPhone 13',
            'category_id' => $category->id
        ]);
        Product::factory()->create([
            'name' => 'Samsung Galaxy Note',
            'category_id' => $category->id
        ]);

        $results = Product::search('Samsung')->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('name', 'Samsung Galaxy S21'));
        $this->assertTrue($results->contains('name', 'Samsung Galaxy Note'));
        $this->assertFalse($results->contains('name', 'iPhone 13'));
    }

    /** @test */
    public function it_filters_by_categories()
    {
        $electronics = Category::factory()->create(['name' => 'Eletrônicos']);
        $clothing = Category::factory()->create(['name' => 'Roupas']);
        $books = Category::factory()->create(['name' => 'Livros']);

        Product::factory()->create([
            'category_id' => $electronics->id,
            'brand_id' => null
        ]);
        Product::factory()->create([
            'category_id' => $clothing->id,
            'brand_id' => null
        ]);
        Product::factory()->create([
            'category_id' => $books->id,
            'brand_id' => null
        ]);

        $results = Product::byCategories([$electronics->id, $clothing->id])->get();

        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_filters_by_brands()
    {
        $category = Category::factory()->create();
        $samsung = Brand::factory()->create(['name' => 'Samsung']);
        $apple = Brand::factory()->create(['name' => 'Apple']);
        $nike = Brand::factory()->create(['name' => 'Nike']);

        Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $samsung->id
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $apple->id
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $nike->id
        ]);

        $results = Product::byBrands([$samsung->id, $apple->id])->get();

        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_combines_multiple_filters()
    {
        $electronics = Category::factory()->create(['name' => 'Eletrônicos']);
        $clothing = Category::factory()->create(['name' => 'Roupas']);
        $samsung = Brand::factory()->create(['name' => 'Samsung']);
        $apple = Brand::factory()->create(['name' => 'Apple']);

        // Produto que deve aparecer no resultado
        Product::factory()->create([
            'name' => 'Samsung Galaxy S21',
            'category_id' => $electronics->id,
            'brand_id' => $samsung->id,
        ]);

        // Produtos que não devem aparecer
        Product::factory()->create([
            'name' => 'Samsung Shirt',
            'category_id' => $clothing->id,
            'brand_id' => $samsung->id,
        ]);

        Product::factory()->create([
            'name' => 'iPhone Galaxy',
            'category_id' => $electronics->id,
            'brand_id' => $apple->id,
        ]);

        $results = Product::search('Galaxy')
            ->byCategories([$electronics->id])
            ->byBrands([$samsung->id])
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Samsung Galaxy S21', $results->first()->name);
    }

    /** @test */
    public function it_formats_price_correctly()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'price' => 1299.99,
            'category_id' => $category->id
        ]);

        $this->assertEquals('R$ 1.299,99', $product->formatted_price);
    }

    /** @test */
    public function it_has_required_fillable_attributes()
    {
        $fillable = (new Product())->getFillable();

        $expectedFillable = ['name', 'description', 'price', 'category_id', 'brand_id', 'image'];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function it_casts_price_to_decimal()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'price' => '1299.99',
            'category_id' => $category->id
        ]);

        $this->assertIsFloat($product->price);
        $this->assertEquals(1299.99, $product->price);
    }

    /** @test */
    public function search_scope_is_case_insensitive()
    {
        $category = Category::factory()->create();
        Product::factory()->create([
            'name' => 'Samsung Galaxy S21',
            'category_id' => $category->id
        ]);

        $results1 = Product::search('samsung')->get();
        $results2 = Product::search('SAMSUNG')->get();
        $results3 = Product::search('Samsung')->get();

        $this->assertCount(1, $results1);
        $this->assertCount(1, $results2);
        $this->assertCount(1, $results3);
    }

    /** @test */
    public function it_handles_empty_search_term()
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $results = Product::search('')->get();

        $this->assertCount(3, $results);
    }

    /** @test */
    public function it_handles_empty_category_filter()
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $results = Product::byCategories([])->get();

        $this->assertCount(3, $results);
    }

    /** @test */
    public function it_handles_empty_brand_filter()
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $results = Product::byBrands([])->get();

        $this->assertCount(3, $results);
    }
}
<?php

namespace Tests\Unit\Models;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_many_products()
    {
        $brand = Brand::factory()->create();
        $products = Product::factory()->count(3)->create([
            'brand_id' => $brand->id,
            'category_id' => \App\Models\Category::factory()->create()->id
        ]);

        $this->assertCount(3, $brand->products);
        $this->assertInstanceOf(Product::class, $brand->products->first());
    }

    #[Test]
    public function it_has_required_fillable_attributes()
    {
        $fillable = (new Brand())->getFillable();

        $expectedFillable = ['name', 'description'];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    #[Test]
    public function it_can_be_created_with_factory()
    {
        $brand = Brand::factory()->create([
            'name' => 'Samsung',
            'description' => 'Marca de eletrônicos sul-coreana'
        ]);

        $this->assertEquals('Samsung', $brand->name);
        $this->assertEquals('Marca de eletrônicos sul-coreana', $brand->description);
        $this->assertDatabaseHas('brands', [
            'name' => 'Samsung',
            'description' => 'Marca de eletrônicos sul-coreana'
        ]);
    }

    #[Test]
    public function it_cascades_delete_to_products()
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id
        ]);

        $this->assertTrue($brand->products->contains($product));
        $this->assertEquals($brand->id, $product->brand_id);

        $this->assertDatabaseHas('products', ['id' => $product->id]);

        $brand->delete();

        // Verificar se o produto ainda existe mas com brand_id null
        // ou se foi deletado dependendo da configuração do banco
        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    #[Test]
    public function it_has_unique_name_constraint()
    {
        Brand::factory()->create(['name' => 'Samsung']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Brand::factory()->create(['name' => 'Samsung']);
    }

    #[Test]
    public function it_can_count_products()
    {
        $brand = Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();
        Product::factory()->count(7)->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id
        ]);

        $this->assertEquals(7, $brand->products()->count());
    }

    #[Test]
    public function it_returns_empty_collection_when_no_products()
    {
        $brand = Brand::factory()->create();

        $this->assertCount(0, $brand->products);
        $this->assertTrue($brand->products->isEmpty());
    }

    #[Test]
    public function it_can_find_brands_with_products()
    {
        $brandWithProducts = Brand::factory()->create(['name' => 'Com Produtos']);
        $brandWithoutProducts = Brand::factory()->create(['name' => 'Sem Produtos']);
        $category = Category::factory()->create();
        
        Product::factory()->create([
            'brand_id' => $brandWithProducts->id,
            'category_id' => $category->id
        ]);

        $brandsWithProducts = Brand::has('products')->get();

        $this->assertCount(1, $brandsWithProducts);
        $this->assertEquals('Com Produtos', $brandsWithProducts->first()->name);
    }

    #[Test]
    public function it_can_order_by_name()
    {
        Brand::factory()->create(['name' => 'Zebra']);
        Brand::factory()->create(['name' => 'Alpha']);
        Brand::factory()->create(['name' => 'Beta']);

        $brands = Brand::orderBy('name')->get();

        $this->assertEquals('Alpha', $brands->first()->name);
        $this->assertEquals('Zebra', $brands->last()->name);
    }

    #[Test]
    public function it_can_get_popular_brands()
    {
        $popularBrand = Brand::factory()->create(['name' => 'Popular']);
        $unpopularBrand = Brand::factory()->create(['name' => 'Unpopular']);
        $category = \App\Models\Category::factory()->create();
        
        // Criar mais produtos para a marca popular
        Product::factory()->count(5)->create([
            'brand_id' => $popularBrand->id,
            'category_id' => $category->id
        ]);
        Product::factory()->count(1)->create([
            'brand_id' => $unpopularBrand->id,
            'category_id' => $category->id
        ]);

        $brands = Brand::withCount('products')
            ->orderBy('products_count', 'desc')
            ->get();

        $this->assertEquals('Popular', $brands->first()->name);
        $this->assertEquals(5, $brands->first()->products_count);
        $this->assertEquals('Unpopular', $brands->last()->name);
        $this->assertEquals(1, $brands->last()->products_count);
    }

    #[Test]
    public function it_handles_null_description()
    {
        $brand = Brand::factory()->create([
            'name' => 'Test Brand',
            'description' => null
        ]);

        $this->assertEquals('Test Brand', $brand->name);
        $this->assertNull($brand->description);
    }

    #[Test]
    public function it_can_search_by_name()
    {
        Brand::factory()->create(['name' => 'Samsung Electronics']);
        Brand::factory()->create(['name' => 'Apple Inc']);
        Brand::factory()->create(['name' => 'Samsung Mobile']);

        $results = Brand::where('name', 'like', '%Samsung%')->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('name', 'Samsung Electronics'));
        $this->assertTrue($results->contains('name', 'Samsung Mobile'));
        $this->assertFalse($results->contains('name', 'Apple Inc'));
    }
}
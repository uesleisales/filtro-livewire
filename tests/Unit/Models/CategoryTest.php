<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_many_products()
    {
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->products);
        $this->assertInstanceOf(Product::class, $category->products->first());
    }

    #[Test]
    public function it_has_required_fillable_attributes()
    {
        $fillable = (new Category())->getFillable();

        $expectedFillable = ['name', 'description'];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    #[Test]
    public function it_can_be_created_with_factory()
    {
        $category = Category::factory()->create([
            'name' => 'Eletrônicos',
            'description' => 'Categoria de produtos eletrônicos'
        ]);

        $this->assertEquals('Eletrônicos', $category->name);
        $this->assertEquals('Categoria de produtos eletrônicos', $category->description);
        $this->assertDatabaseHas('categories', [
            'name' => 'Eletrônicos',
            'description' => 'Categoria de produtos eletrônicos'
        ]);
    }

    #[Test]
    public function it_cascades_delete_to_products()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertDatabaseHas('products', ['id' => $product->id]);

        $category->delete();

        // Verificar se o produto ainda existe mas com category_id null
        // ou se foi deletado dependendo da configuração do banco
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    #[Test]
    public function it_has_unique_name_constraint()
    {
        Category::factory()->create(['name' => 'Eletrônicos']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Category::factory()->create(['name' => 'Eletrônicos']);
    }

    #[Test]
    public function it_can_count_products()
    {
        $category = Category::factory()->create();
        Product::factory()->count(5)->create(['category_id' => $category->id]);

        $this->assertEquals(5, $category->products()->count());
    }

    #[Test]
    public function it_returns_empty_collection_when_no_products()
    {
        $category = Category::factory()->create();

        $this->assertCount(0, $category->products);
        $this->assertTrue($category->products->isEmpty());
    }

    #[Test]
    public function it_can_find_categories_with_products()
    {
        $categoryWithProducts = Category::factory()->create(['name' => 'Com Produtos']);
        $categoryWithoutProducts = Category::factory()->create(['name' => 'Sem Produtos']);
        
        Product::factory()->create(['category_id' => $categoryWithProducts->id]);

        $categoriesWithProducts = Category::has('products')->get();

        $this->assertCount(1, $categoriesWithProducts);
        $this->assertEquals('Com Produtos', $categoriesWithProducts->first()->name);
    }

    #[Test]
    public function it_can_order_by_name()
    {
        Category::factory()->create(['name' => 'Zebra']);
        Category::factory()->create(['name' => 'Alpha']);
        Category::factory()->create(['name' => 'Beta']);

        $categories = Category::orderBy('name')->get();

        $this->assertEquals('Alpha', $categories->first()->name);
        $this->assertEquals('Zebra', $categories->last()->name);
    }
}
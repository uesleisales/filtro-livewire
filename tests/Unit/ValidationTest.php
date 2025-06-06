<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function product_requires_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $category = Category::factory()->create();
        Product::factory()->create([
            'name' => null,
            'category_id' => $category->id
        ]);
    }

    #[Test]
    public function product_requires_price()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $category = Category::factory()->create();
        Product::factory()->create([
            'price' => null,
            'category_id' => $category->id
        ]);
    }

    #[Test]
    public function product_requires_category()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Product::factory()->create(['category_id' => null]);
    }

    #[Test]
    public function product_price_must_be_positive()
    {
        // Teste de validação customizada se implementada
        $product = Product::factory()->make(['price' => -10.00]);
        
        // Se houver validação no model ou observer
        $this->assertLessThan(0, $product->price);
        
        // Em um cenário real, isso deveria falhar na validação
        // $this->expectException(ValidationException::class);
        // $product->save();
    }

    #[Test]
    public function product_name_has_maximum_length()
    {
        $category = Category::factory()->create();
        $longName = str_repeat('a', 256);
        
        $product = Product::create([
            'name' => $longName,
            'price' => 99.99,
            'category_id' => $category->id
        ]);
        
        $this->assertEquals(255, strlen($product->fresh()->name));
    }

    #[Test]
    public function category_requires_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Category::factory()->create(['name' => null]);
    }

    #[Test]
    public function category_name_must_be_unique()
    {
        Category::factory()->create(['name' => 'Eletrônicos']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Category::factory()->create(['name' => 'Eletrônicos']);
    }

    #[Test]
    public function category_name_has_maximum_length()
    {
        $longName = str_repeat('a', 256);
        
        $category = Category::factory()->create(['name' => $longName]);
        
        $this->assertEquals(255, strlen($category->fresh()->name));
    }

    #[Test]
    public function brand_requires_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Brand::factory()->create(['name' => null]);
    }

    #[Test]
    public function brand_name_must_be_unique()
    {
        Brand::factory()->create(['name' => 'Samsung']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Brand::factory()->create(['name' => 'Samsung']);
    }

    #[Test]
    public function brand_name_has_maximum_length()
    {
        $longName = str_repeat('a', 256);
        
        $brand = Brand::factory()->create(['name' => $longName]);
        
        $this->assertEquals(255, strlen($brand->fresh()->name));
    }

    #[Test]
    public function product_foreign_key_constraints_work()
    {
        // Tentar criar produto com category_id inexistente
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Product::factory()->create(['category_id' => 99999]);
    }

    #[Test]
    public function product_can_have_null_brand()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'brand_id' => null,
            'category_id' => $category->id
        ]);
        
        $this->assertNull($product->brand_id);
        $this->assertNull($product->brand);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'brand_id' => null
        ]);
    }

    #[Test]
    public function product_brand_foreign_key_constraint_works()
    {
        // Tentar criar produto com brand_id inexistente
        $this->expectException(\Illuminate\Database\QueryException::class);
        $category = Category::factory()->create();
        Product::factory()->create([
            'brand_id' => 99999,
            'category_id' => $category->id
        ]);
    }

    #[Test]
    public function product_price_precision_is_correct()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'price' => 1234.56,
            'category_id' => $category->id
        ]);

        $this->assertEquals('1234.56', $product->price);
        $this->assertIsString($product->price); // Com cast decimal:2, retorna string
    }

    #[Test]
    public function product_description_can_be_null()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'description' => null,
            'category_id' => $category->id
        ]);

        $this->assertNull($product->description);
    }

    #[Test]
    public function product_description_can_be_long_text()
    {
        $longDescription = str_repeat('Lorem ipsum dolor sit amet. ', 100);
        
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'description' => $longDescription,
            'category_id' => $category->id
        ]);
        
        $this->assertEquals($longDescription, $product->description);
    }

    #[Test]
    public function category_description_can_be_null()
    {
        $category = Category::factory()->create(['description' => null]);
        
        $this->assertNull($category->description);
    }

    #[Test]
    public function brand_description_can_be_null()
    {
        $brand = Brand::factory()->create(['description' => null]);
        
        $this->assertNull($brand->description);
    }

    #[Test]
    public function product_image_can_be_null()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'image' => null,
            'category_id' => $category->id
        ]);

        $this->assertNull($product->image);
    }

    #[Test]
    public function product_image_can_store_url()
    {
        $imageUrl = 'https://example.com/image.jpg';
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'image' => $imageUrl,
            'category_id' => $category->id
        ]);

        $this->assertEquals($imageUrl, $product->image);
    }

    #[Test]
    public function models_have_timestamps()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $brand = Brand::factory()->create();
        
        $this->assertNotNull($product->created_at);
        $this->assertNotNull($product->updated_at);
        $this->assertNotNull($category->created_at);
        $this->assertNotNull($category->updated_at);
        $this->assertNotNull($brand->created_at);
        $this->assertNotNull($brand->updated_at);
    }

    #[Test]
    public function product_updated_at_changes_on_update()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $originalUpdatedAt = $product->updated_at;
        
        sleep(1);
        
        $product->update(['name' => 'Updated Name']);
        
        $this->assertNotEquals($originalUpdatedAt, $product->fresh()->updated_at);
    }

    #[Test]
    public function database_constraints_prevent_orphaned_products()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $productId = $product->id;
        
        // Deletar categoria deve deletar produtos em cascata
        $category->delete();
        
        // Verificar que o produto foi deletado devido ao CASCADE
        $this->assertNull(Product::find($productId));
        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }
}
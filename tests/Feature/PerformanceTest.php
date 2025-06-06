<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar dados de teste em massa
        $this->createLargeDataset();
    }

    private function createLargeDataset(): void
    {
        // Criar 10 categorias
        $categories = Category::factory()->count(10)->create();
        
        // Criar 20 marcas
        $brands = Brand::factory()->count(20)->create();
        
        // Criar 1000 produtos
        Product::factory()->count(1000)->create([
            'category_id' => fn() => $categories->random()->id,
            'brand_id' => fn() => $brands->random()->id,
        ]);
    }

    #[Test]
    public function it_performs_efficient_product_queries()
    {
        // Resetar contador de queries
        DB::enableQueryLog();
        
        $category = Category::first();
        $brand = Brand::first();
        
        // Executar consulta complexa
        $products = Product::with(['category', 'brand'])
            ->search('Product')
            ->byCategories([$category->id])
            ->byBrands([$brand->id])
            ->paginate(10);
        
        $queryLog = DB::getQueryLog();
        
        // Verificar que não há N+1 queries
        $this->assertLessThanOrEqual(3, count($queryLog), 'Muitas queries executadas - possível problema N+1');
        
        // Verificar que os relacionamentos foram carregados
        if ($products->count() > 0) {
            $firstProduct = $products->first();
            $this->assertNotNull($firstProduct->category);
            $this->assertNotNull($firstProduct->brand);
        }
        
        DB::disableQueryLog();
    }

    #[Test]
    public function it_caches_category_list_efficiently()
    {
        Cache::flush();
        
        DB::enableQueryLog();
        $categories1 = Cache::remember('categories.all', 3600, function () {
            return Category::orderBy('name')->get();
        });
        $firstCallQueries = count(DB::getQueryLog());
        DB::disableQueryLog();
        
        DB::flushQueryLog();
        DB::enableQueryLog();
        $categories2 = Cache::remember('categories.all', 3600, function () {
            return Category::orderBy('name')->get();
        });
        $secondCallQueries = count(DB::getQueryLog());
        DB::disableQueryLog();
        
        $this->assertGreaterThan(0, $firstCallQueries);
        $this->assertEquals(0, $secondCallQueries);
        $this->assertEquals($categories1->count(), $categories2->count());
    }

    #[Test]
    public function it_caches_brand_list_efficiently()
    {
        Cache::flush();
        
        DB::enableQueryLog();
        $brands1 = Cache::remember('brands.all', 3600, function () {
            return Brand::orderBy('name')->get();
        });
        $firstCallQueries = count(DB::getQueryLog());
        DB::disableQueryLog();
        
        DB::flushQueryLog();
        DB::enableQueryLog();
        $brands2 = Cache::remember('brands.all', 3600, function () {
            return Brand::orderBy('name')->get();
        });
        $secondCallQueries = count(DB::getQueryLog());
        DB::disableQueryLog();
        
        $this->assertGreaterThan(0, $firstCallQueries);
        $this->assertEquals(0, $secondCallQueries);
        $this->assertEquals($brands1->count(), $brands2->count());
    }

    #[Test]
    public function it_handles_large_result_sets_efficiently()
    {
        $startTime = microtime(true);
        
        $products = Product::with(['category', 'brand'])->paginate(50);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(2.0, $executionTime, 'Consulta muito lenta para dataset grande');
        
        $this->assertGreaterThan(0, $products->count());
        $this->assertLessThanOrEqual(50, $products->count());
    }

    #[Test]
    public function it_optimizes_search_queries()
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        $firstProduct = Product::first();
        $searchTerm = substr($firstProduct->name, 0, 5);
        
        $results1 = Product::searchByName($searchTerm)->limit(10)->get();
        
        $category = Category::first();
        $results2 = Product::searchByName($searchTerm)
            ->byCategories([$category->id])
            ->limit(10)
            ->get();
        
        $queryLog = DB::getQueryLog();
        
        $this->assertLessThanOrEqual(4, count($queryLog));
        
        $this->assertGreaterThan(0, $results1->count());
        
        DB::disableQueryLog();
    }

    #[Test]
    public function it_handles_concurrent_cache_access()
    {
        Cache::flush();
        
        $cacheKey = 'test.concurrent.access';
        $cacheValue = 'test-value';
        
        $results = [];
        
        for ($i = 0; $i < 5; $i++) {
            $results[] = Cache::remember($cacheKey, 3600, function () use ($cacheValue) {
                usleep(100000);
                return $cacheValue;
            });
        }
        
        foreach ($results as $result) {
            $this->assertEquals($cacheValue, $result);
        }
        
        $this->assertEquals($cacheValue, Cache::get($cacheKey));
    }

    #[Test]
    public function it_measures_filter_performance_with_indexes()
    {
        $startTime = microtime(true);
        
        $category = Category::first();
        $brand = Brand::first();
        
        $products = Product::where('category_id', $category->id)
            ->where('brand_id', $brand->id)
            ->where('name', 'like', '%Product%')
            ->limit(20)
            ->get();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(0.5, $executionTime, 'Consulta com filtros muito lenta');
    }

    #[Test]
    public function it_tests_pagination_performance()
    {
        $startTime = microtime(true);
        
        $products = Product::with(['category', 'brand'])
            ->paginate(20, ['*'], 'page', 10);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(1.0, $executionTime, 'Paginação em páginas avançadas muito lenta');
        
        $this->assertLessThanOrEqual(20, $products->count());
        $this->assertEquals(10, $products->currentPage());
    }

    #[Test]
    public function it_tests_memory_usage_with_large_datasets()
    {
        $initialMemory = memory_get_usage();
        
        $products = Product::with(['category', 'brand'])->limit(500)->get();
        
        $finalMemory = memory_get_usage();
        $memoryUsed = $finalMemory - $initialMemory;
        
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed, 'Uso excessivo de memória');
        
        $this->assertGreaterThan(0, $products->count());
    }

    #[Test]
    public function it_tests_cache_invalidation()
    {
        $cacheKey = 'products.filtered';
        
        $initialProducts = Cache::remember($cacheKey, 3600, function () {
            return Product::limit(10)->get();
        });
        
        $this->assertTrue(Cache::has($cacheKey));
        
        Cache::forget($cacheKey);
        
        $this->assertFalse(Cache::has($cacheKey));
        
        $newProducts = Cache::remember($cacheKey, 3600, function () {
            return Product::limit(10)->get();
        });
        
        $this->assertTrue(Cache::has($cacheKey));
        $this->assertEquals($initialProducts->count(), $newProducts->count());
    }
}
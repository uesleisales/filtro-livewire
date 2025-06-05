<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se existem categorias e marcas
        $categoriesCount = Category::count();
        $brandsCount = Brand::count();
        
        if ($categoriesCount === 0 || $brandsCount === 0) {
            $this->command->error('É necessário ter categorias e marcas antes de criar produtos!');
            return;
        }
        
        // Obter IDs existentes para relacionamentos
        $categoryIds = Category::pluck('id')->toArray();
        $brandIds = Brand::pluck('id')->toArray();
        
        // Criar 150 produtos com relacionamentos existentes
        for ($i = 0; $i < 150; $i++) {
            Product::factory()->create([
                'category_id' => fake()->randomElement($categoryIds),
                'brand_id' => fake()->randomElement($brandIds),
            ]);
        }
        
        $this->command->info('150 produtos criados com sucesso!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar 15 categorias usando a factory
        Category::factory()->count(15)->create();
        
        $this->command->info('15 categorias criadas com sucesso!');
    }
}

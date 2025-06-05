<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar 20 marcas usando a factory
        Brand::factory()->count(20)->create();
        
        $this->command->info('20 marcas criadas com sucesso!');
    }
}

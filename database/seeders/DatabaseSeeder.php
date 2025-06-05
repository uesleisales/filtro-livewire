<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando população do banco de dados...');
        
        // Executar seeders na ordem correta (devido aos relacionamentos)
        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
        ]);
        
        // Criar usuário de teste
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        $this->command->info('✅ Banco de dados populado com sucesso!');
        $this->command->info('📊 Resumo:');
        $this->command->info('   - Categorias: ' . \App\Models\Category::count());
        $this->command->info('   - Marcas: ' . \App\Models\Brand::count());
        $this->command->info('   - Produtos: ' . \App\Models\Product::count());
    }
}

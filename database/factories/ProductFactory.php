<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productNames = [
            'Smartphone Galaxy',
            'iPhone Pro Max',
            'Notebook Dell Inspiron',
            'Tênis Nike Air',
            'Camiseta Adidas',
            'Smart TV Samsung',
            'Fone de Ouvido Sony',
            'Tablet Apple iPad',
            'Mouse Gamer Logitech',
            'Teclado Mecânico',
            'Monitor LG UltraWide',
            'Câmera Canon EOS',
            'Relógio Apple Watch',
            'Perfume Chanel',
            'Livro Bestseller',
            'Jogo PlayStation',
            'Cafeteira Nespresso',
            'Aspirador Electrolux',
            'Geladeira Brastemp',
            'Micro-ondas Panasonic'
        ];

        $name = $this->faker->randomElement($productNames) . ' ' . $this->faker->word();
        
        return [
            'name' => $name,
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 10, 5000),
            'sku' => strtoupper($this->faker->bothify('??###??')),
            'stock' => $this->faker->numberBetween(0, 100),
            'active' => $this->faker->boolean(85), // 85% chance de estar ativo
            'image' => $this->faker->optional(0.8)->randomElement([
                'https://via.placeholder.com/400x400/0066CC/FFFFFF?text=Produto+1',
                'https://via.placeholder.com/400x400/FF6600/FFFFFF?text=Produto+2',
                'https://via.placeholder.com/400x400/009900/FFFFFF?text=Produto+3',
                'https://via.placeholder.com/400x400/CC0066/FFFFFF?text=Produto+4',
                'https://via.placeholder.com/400x400/6600CC/FFFFFF?text=Produto+5',
                'https://via.placeholder.com/400x400/CC6600/FFFFFF?text=Produto+6',
                'https://via.placeholder.com/400x400/0099CC/FFFFFF?text=Produto+7',
                'https://via.placeholder.com/400x400/CC9900/FFFFFF?text=Produto+8'
            ]),
            'category_id' => Category::factory(),
            'brand_id' => Brand::inRandomOrder()->first()?->id ?: Brand::factory(),
        ];
    }
}

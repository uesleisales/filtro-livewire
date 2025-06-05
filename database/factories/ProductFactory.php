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
            'image' => $this->faker->optional(0.8)->imageUrl(400, 400, 'technics', true),
            // category_id e brand_id serão definidos no seeder
        ];
    }
}

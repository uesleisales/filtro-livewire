<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Eletrônicos',
            'Roupas e Acessórios',
            'Casa e Jardim',
            'Esportes e Lazer',
            'Livros e Mídia',
            'Beleza e Cuidados Pessoais',
            'Automotivo',
            'Brinquedos e Jogos',
            'Alimentação e Bebidas',
            'Saúde e Bem-estar',
            'Móveis e Decoração',
            'Ferramentas e Construção',
            'Pet Shop',
            'Informática',
            'Telefonia'
        ]);

        return [
            'name' => $name,
            'description' => $this->faker->sentence(10),
        ];
    }
}

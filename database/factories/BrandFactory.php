<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Samsung',
            'Apple',
            'Nike',
            'Adidas',
            'Sony',
            'LG',
            'Philips',
            'Motorola',
            'Xiaomi',
            'Dell',
            'HP',
            'Lenovo',
            'Asus',
            'Microsoft',
            'Google',
            'Amazon',
            'Coca-Cola',
            'Pepsi',
            'Nestlé',
            'Unilever',
            'Procter & Gamble',
            'Johnson & Johnson',
            'L\'Oréal',
            'Toyota',
            'Honda',
            'Ford',
            'Volkswagen',
            'BMW',
            'Mercedes-Benz',
            'Audi'
        ]);

        return [
            'name' => $name,
            'description' => $this->faker->sentence(8),
            'logo' => $this->faker->optional(0.7)->imageUrl(200, 200, 'business', true, $name),
        ];
    }
}

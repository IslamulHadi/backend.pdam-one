<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TariffCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TariffCategory>
 */
class TariffCategoryFactory extends Factory
{
    protected $model = TariffCategory::class;

    public function definition(): array
    {
        $types = ['rumah_tangga', 'niaga', 'industri', 'sosial'];

        return [
            'code' => fake()->unique()->regexify('[1-3][A-C][1-3]?'),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement($types),
            'tier_1_price' => fake()->numberBetween(1000, 5000),
            'tier_2_price' => fake()->numberBetween(3000, 7000),
            'tier_3_price' => fake()->numberBetween(5000, 10000),
            'tier_4_price' => fake()->numberBetween(7000, 15000),
            'subscription_fee' => fake()->numberBetween(10000, 50000),
            'building_area_requirement' => 'Luas bangunan < ' . fake()->numberBetween(36, 100) . ' m²',
            'electricity_power_requirement' => 'Daya listrik max ' . fake()->randomElement([450, 900, 1300, 2200]) . ' VA',
            'road_width_requirement' => 'Lebar jalan depan < ' . fake()->numberBetween(3, 8) . ' meter',
            'display_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }

    public function rumahTangga(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'rumah_tangga',
        ]);
    }

    public function niaga(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'niaga',
        ]);
    }

    public function industri(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'industri',
        ]);
    }

    public function sosial(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sosial',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}


<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CompanyInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyInfo>
 */
class CompanyInfoFactory extends Factory
{
    protected $model = CompanyInfo::class;

    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'value' => fake()->sentence(),
            'group' => fake()->randomElement(['contact', 'social', 'about', 'general']),
        ];
    }

    public function contact(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => 'contact',
        ]);
    }

    public function social(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => 'social',
        ]);
    }

    public function about(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => 'about',
        ]);
    }
}


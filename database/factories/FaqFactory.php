<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FaqCategory;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'question' => fake()->sentence().'?',
            'answer' => fake()->paragraph(2),
            'category' => fake()->randomElement(FaqCategory::cases()),
            'display_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function pengaduan(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => FaqCategory::Pengaduan,
        ]);
    }

    public function umum(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => FaqCategory::Umum,
        ]);
    }
}

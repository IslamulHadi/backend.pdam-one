<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PejabatLevel;
use App\Models\Pejabat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pejabat>
 */
class PejabatFactory extends Factory
{
    protected $model = Pejabat::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'jabatan' => fake()->jobTitle(),
            'level' => fake()->randomElement(PejabatLevel::cases()),
            'bidang' => fake()->randomElement(['Teknik', 'Umum', 'Keuangan', 'Langganan', 'Produksi']),
            'deskripsi' => fake()->optional()->sentence(),
            'display_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }

    public function direksi(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => PejabatLevel::Direksi,
        ]);
    }

    public function kabid(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => PejabatLevel::Kabid,
        ]);
    }

    public function kasubid(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => PejabatLevel::Kasubid,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

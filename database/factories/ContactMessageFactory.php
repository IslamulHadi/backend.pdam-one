<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        $categories = ['umum', 'layanan', 'tagihan', 'pengaduan', 'kerjasama', 'lainnya'];

        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'category' => fake()->randomElement($categories),
            'message' => fake()->paragraph(3),
            'is_read' => false,
            'responded_at' => null,
            'response' => null,
            'responded_by' => null,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    public function responded(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'response' => fake()->paragraph(2),
            'responded_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'responded_by' => User::factory(),
        ]);
    }

    public function respondedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'response' => fake()->paragraph(2),
            'responded_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'responded_by' => $user->id,
        ]);
    }
}


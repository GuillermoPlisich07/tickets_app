<?php

namespace Database\Factories;

use App\Enums\AuthorType;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->paragraph(),
            'author' => $this->faker->name(),
            'author_type' => $this->faker->randomElement(AuthorType::cases())->value,
            'is_root' => false,
        ];
    }

    public function root(): static
    {
        return $this->state(['is_root' => true, 'author_type' => AuthorType::Customer->value]);
    }
}

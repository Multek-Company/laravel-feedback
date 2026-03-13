<?php

namespace Multek\LaravelFeedback\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Multek\LaravelFeedback\Models\Feedback;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['bug', 'feature', 'praise', 'ux-issue']),
            'content' => fake()->paragraph(),
            'metadata' => null,
        ];
    }

    public function withMetadata(array $metadata = []): static
    {
        return $this->state(fn (array $attributes) => [
            'metadata' => $metadata ?: [
                'browser' => 'Chrome 120',
                'page_url' => fake()->url(),
            ],
        ]);
    }

    public function bug(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'bug',
        ]);
    }

    public function feature(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'feature',
        ]);
    }
}

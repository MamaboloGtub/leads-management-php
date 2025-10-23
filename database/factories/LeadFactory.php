<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'lead_status' => fake()->randomElement(['new', 'contacted', 'qualified', 'converted', 'lost']),
            'lead_source' => fake()->randomElement(['website', 'social_media', 'referral', 'advertising', 'cold_call', 'email']),
        ];
    }

    /**
     * Indicate that the lead status is new.
     */
    public function newStatus(): static
    {
        return $this->state(fn (array $attributes) => [
            'lead_status' => 'new',
        ]);
    }

    /**
     * Indicate that the lead has been contacted.
     */
    public function contacted(): static
    {
        return $this->state(fn (array $attributes) => [
            'lead_status' => 'contacted',
        ]);
    }

    /**
     * Indicate that the lead is qualified.
     */
    public function qualified(): static
    {
        return $this->state(fn (array $attributes) => [
            'lead_status' => 'qualified',
        ]);
    }

    /**
     * Indicate that the lead is converted.
     */
    public function converted(): static
    {
        return $this->state(fn (array $attributes) => [
            'lead_status' => 'converted',
        ]);
    }
}
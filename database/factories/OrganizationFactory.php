<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->company();
        return [
            'parent_id' => 1,

            // Basic information
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 9999),

            // Contact information
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'vat' => fake()->numerify('VAT-##########'),

            // Organization branding
            'logo' => null,
            'favicon' => null,

            // Address
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => fake()->country(),
            'postal_code' => fake()->postcode(),

            // Status
            'is_active' => true,

            // Trial
            'trial_ends_at' => now()->addDays(
                fake()->numberBetween(7, 30)
            ),

            // Subscription
            'subscription_status' => 'trial',
            'subscription_ends_at' => null,
        ];
    }


    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive organization.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create an organization with an active subscription.
     */
    public function subscribed(): static
    {
        return $this->state(fn(array $attributes) => [
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonths(
                fake()->numberBetween(1, 12)
            ),
            'trial_ends_at' => null,
        ]);
    }

    /**
     * Create an expired subscription.
     */
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'subscription_status' => 'expired',
            'subscription_ends_at' => now()->subDays(
                fake()->numberBetween(1, 30)
            ),
            'trial_ends_at' => null,
        ]);
    }
}

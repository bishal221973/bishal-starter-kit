<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::factory()
            ->count(500)
            ->create()
            ->each(function (Organization $organization) {
                // Create branches
                Organization::factory()
                    ->count(fake()->numberBetween(2, 4))
                    ->create([
                        'parent_id' => $organization->id,
                    ]);
            });
    }
}

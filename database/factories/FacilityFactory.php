<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'facility_type' => 'clinic',
            'phone_number' => fake()->numerify('09########'),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}

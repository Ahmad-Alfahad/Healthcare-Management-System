<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Facility;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'facility_id' => Facility::factory(),
            'is_active' => true,
        ];
    }
}

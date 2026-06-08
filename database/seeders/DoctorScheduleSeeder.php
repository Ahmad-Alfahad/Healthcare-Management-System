<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = [

            [
                'doctor_id' => 1,
                'day_of_week' => 'monday',
                'is_off' => false,
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'avg_consultation_time' => 30,
            ],

            [
                'doctor_id' => 1,
                'day_of_week' => 'tuesday',
                'is_off' => false,
                'start_time' => '10:00:00',
                'end_time' => '15:00:00',
                'avg_consultation_time' => 20,
            ],

            [
                'doctor_id' => 1,
                'day_of_week' => 'friday',
                'is_off' => true,
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'avg_consultation_time' => 30,
            ],


            [
                'doctor_id' => 1,
                'day_of_week' => 'wednesday',
                'is_off' => false,
                'start_time' => '14:00:00',
                'end_time' => '18:00:00',
                'avg_consultation_time' => 45,
            ],

            [
                'doctor_id' => 1,
                'day_of_week' => 'sunday',
                'is_off' => false,
                'start_time' => '09:30:00',
                'end_time' => '16:30:00',
                'avg_consultation_time' => 30,
            ],
        ];

        foreach ($schedules as $schedule) {

            DoctorSchedule::create($schedule);

        }
    }
}
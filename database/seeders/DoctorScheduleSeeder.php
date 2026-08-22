<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $doctorOne = Doctor::whereHas('profile', fn($query) => $query->where('national_number', '010100223344'))->firstOrFail();
        $doctorTwo = Doctor::whereHas('profile', fn($query) => $query->where('national_number', '020200556677'))->firstOrFail();

        $schedules = [

            [
                'doctor_id' => $doctorOne->id,
                'day_of_week' => 'Monday',
                'is_off' => false,
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'avg_consultation_time' => 30,
            ],

            [
                'doctor_id' => $doctorOne->id,
                'day_of_week' => 'Tuesday',
                'is_off' => false,
                'start_time' => '10:00:00',
                'end_time' => '15:00:00',
                'avg_consultation_time' => 20,
            ],

            [
                'doctor_id' => $doctorOne->id,
                'day_of_week' => 'Friday',
                'is_off' => true,
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'avg_consultation_time' => 30,
            ],


            [
                'doctor_id' => $doctorTwo->id,
                'day_of_week' => 'Wednesday',
                'is_off' => false,
                'start_time' => '14:00:00',
                'end_time' => '18:00:00',
                'avg_consultation_time' => 45,
            ],

            [
                'doctor_id' => $doctorTwo->id,
                'day_of_week' => 'Sunday',
                'is_off' => false,
                'start_time' => '09:30:00',
                'end_time' => '16:30:00',
                'avg_consultation_time' => 30,
            ],
        ];

        foreach ($schedules as $schedule) {

            DoctorSchedule::updateOrCreate(
                ['doctor_id' => $schedule['doctor_id'], 'day_of_week' => $schedule['day_of_week']],
                $schedule
            );
        }
    }
}

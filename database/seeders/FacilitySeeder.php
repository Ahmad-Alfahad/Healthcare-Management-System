<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainHospital = Facility::updateOrCreate(['name' => 'Al-Amal International Hospital'], [
            'parent_id'     => null,
            'name'          => 'Al-Amal International Hospital',
            'facility_type' => 'hospital',
            'phone_number'  => '+96311223344',
            'address'       => 'Damascus, Mezzeh Street',
        ]);

        $mainLabCenter = Facility::updateOrCreate(['name' => 'Al-Shifa Medical Diagnostic Center'], [
            'parent_id'     => null,
            'name'          => 'Al-Shifa Medical Diagnostic Center',
            'facility_type' => 'laboratory',
            'phone_number'  => '+96321556677',
            'address'       => 'Aleppo, Al-Jamiliyah',
        ]);

        Facility::updateOrCreate(['name' => 'Al-Amal Dental Clinic (Branch 1)'], [
            'parent_id'     => $mainHospital->id,
            'name'          => 'Al-Amal Dental Clinic (Branch 1)',
            'facility_type' => 'clinic',
            'phone_number'  => '+96311223345',
            'address'       => 'Damascus, Malki District',
        ]);

        Facility::updateOrCreate(['name' => 'Al-Amal Pediatrics Clinic (Branch 2)'], [
            'parent_id'     => $mainHospital->id,
            'name'          => 'Al-Amal Pediatrics Clinic (Branch 2)',
            'facility_type' => 'clinic',
            'phone_number'  => '+96311223346',
            'address'       => 'Damascus, Shaalan',
        ]);

        Facility::updateOrCreate(['name' => 'Al-Shifa Lab Express'], [
            'parent_id'     => $mainLabCenter->id,
            'name'          => 'Al-Shifa Lab Express',
            'facility_type' => 'laboratory',
            'phone_number'  => '+96321556678',
            'address'       => 'Aleppo, Shahbaa',
        ]);

        Facility::updateOrCreate(['name' => 'Al-Amal Community Pharmacy'], [
            'parent_id'     => $mainHospital->id,
            'facility_type' => 'pharmacy',
            'phone_number'  => '+96311223347',
            'address'       => 'Damascus, Malki District',
        ]);
    }
}

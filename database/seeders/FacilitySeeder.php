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
        $mainHospital = Facility::create([
            'parent_id'     => null, 
            'name'          => 'Al-Amal International Hospital',
            'facility_type' => 'Hospital',
            'phone_number'  => '+96311223344',
            'address'       => 'Damascus, Mezzeh Street',
        ]);

        $mainLabCenter = Facility::create([
            'parent_id'     => null, 
            'name'          => 'Al-Shifa Medical Diagnostic Center',
            'facility_type' => 'Diagnostic Center',
            'phone_number'  => '+96321556677',
            'address'       => 'Aleppo, Al-Jamiliyah',
        ]);

        Facility::create([
            'parent_id'     => $mainHospital->id, 
            'name'          => 'Al-Amal Dental Clinic (Branch 1)',
            'facility_type' => 'Clinic',
            'phone_number'  => '+96311223345',
            'address'       => 'Damascus, Malki District',
        ]);

        Facility::create([
            'parent_id'     => $mainHospital->id, 
            'name'          => 'Al-Amal Pediatrics Clinic (Branch 2)',
            'facility_type' => 'Clinic',
            'phone_number'  => '+96311223346',
            'address'       => 'Damascus, Shaalan',
        ]);

        Facility::create([
            'parent_id'     => $mainLabCenter->id, 
            'name'          => 'Al-Shifa Lab Express',
            'facility_type' => 'Laboratory',
            'phone_number'  => '+96321556678',
            'address'       => 'Aleppo, Shahbaa',
        ]);
    }
}
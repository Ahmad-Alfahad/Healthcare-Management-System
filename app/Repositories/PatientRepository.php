<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

class PatientRepository
{
    public function getAll()
    {
        return Patient::with('profile')->paginate(15);
    }

    public function findById(Patient $patient)
    {
        return $patient->load('profile');
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $profile = Profile::create([
                'user_id' => $data['user_id'],
                'full_name' => $data['full_name'],
                'national_number' => $data['national_number'] ?? null,
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
            ]);

            return $profile->patient()->create([
                'blood_type' => $data['blood_type'] ?? null,
                'height' => $data['height'] ?? null,
                'weight' => $data['weight'] ?? null,
                'allergies' => $data['allergies'] ?? null,
                'chronic_diseases' => $data['chronic_diseases'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            ]);
        });
    }

    public function update(Patient $patient, array $data)
    {
        return DB::transaction(function () use ($patient, $data) {
            $patient->profile->update(array_intersect_key($data, array_flip([
                'full_name',
                'national_number',
                'phone',
                'gender',
                'address',
                'date_of_birth'
            ])));

            $patient->update(array_intersect_key($data, array_flip([
                'blood_type',
                'height',
                'weight',
                'allergies',
                'chronic_diseases',
                'emergency_contact_name',
                'emergency_contact_phone'
            ])));

            return $patient->load('profile');
        });
    }

    public function delete(Patient $patient)
    {
        return $patient->profile->delete();
    }
}

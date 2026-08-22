<?php

namespace App\Repositories;

use App\Models\Profile;
use App\Models\User;

class ProfileRepository
{
    public function getAll(?User $user = null)
    {
        $query = Profile::with('user.roles');

        if ($user?->isManager()) {
            $facilityId = $user->facility()?->id;
            $query->where(function ($query) use ($facilityId): void {
                $query->whereHas('doctor.facilityDepartmentSpecialization.facilityDepartment', fn($q) => $q->where('facility_id', $facilityId))
                    ->orWhereHas('pharmacist', fn($q) => $q->where('facility_id', $facilityId))
                    ->orWhereHas('labStaff', fn($q) => $q->where('facility_id', $facilityId))
                    ->orWhereHas('patient.appointments.doctor.facilityDepartmentSpecialization.facilityDepartment', fn($q) => $q->where('facility_id', $facilityId));
            });
        }

        return $query->paginate(15);
    }

    public function findById(int $id)
    {
        return Profile::findOrFail($id);
    }

    public function create(array $data)
    {
        return Profile::create($data);
    }

    public function update(Profile $profile, array $data)
    {
        $profile->update($data);
        return $profile;
    }

    public function delete(Profile $profile)
    {
        return $profile->delete();
    }
}

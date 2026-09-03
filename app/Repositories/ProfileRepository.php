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
            $facilityIds = $user->accessibleFacilityIds();
            $query->where(function ($query) use ($facilityIds): void {
                $query->whereHas('employee', fn($q) => $q->whereIn('facility_id', $facilityIds))
                    ->orWhereHas('patient.appointments.doctor.facilityDepartmentSpecialization.facilityDepartment', fn($q) => $q->whereIn('facility_id', $facilityIds));
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

<?php 
namespace App\Repositories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Collection;

class DoctorRepository
{
    public function all(): Collection
    {
        return Doctor::with([
            'profile', 
            'facilityDepartmentSpecialization.specialization', 
            'facilityDepartmentSpecialization.facilityDepartment.facility', 
            'facilityDepartmentSpecialization.facilityDepartment.department'
        ])->get();
    }

    public function find(int $id): Doctor
    {
        return Doctor::with([
            'profile', 
            'facilityDepartmentSpecialization.specialization', 
            'facilityDepartmentSpecialization.facilityDepartment.facility', 
            'facilityDepartmentSpecialization.facilityDepartment.department'
        ])->findOrFail($id);
    }

    public function create(array $data): Doctor
    {
        

        return Doctor::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $doctor = Doctor::findOrFail($id);
        return $doctor->update($data);
    }

    public function deactivate(int $id): bool
    {
        $doctor = Doctor::findOrFail($id);
        return $doctor->update(['is_active' => false]);
    }

    public function delete(int $id): bool
    {
        $doctor = Doctor::findOrFail($id);
        return $doctor->delete();
    }
}

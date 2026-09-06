<?php

namespace App\Repositories;

use App\Models\LabResult;
use App\Models\User;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class LabResultRepository
{
    use ListQuery;

    public function all(array $filters = [], ?User $user = null): LengthAwarePaginator
    {
        $query = LabResult::with([
            'labRequestItem.labTest',
            'labRequestItem.visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'labRequestItem.visit.appointment.doctor.employee.profile',
            'labRequestItem.visit.appointment.patient.profile',
            'labStaff.employee.profile',
            'labStaff.employee.facility',
        ]);

        if ($user !== null && !$user->isAdmin()) {
            if ($user->isDoctor()) {
                $query->whereHas('labRequestItem.visit', fn ($visitQuery) => $visitQuery->where('doctor_id', $user->doctor?->id));
            } elseif ($user->isPatient()) {
                $query->whereHas('labRequestItem.visit', fn ($visitQuery) => $visitQuery->where('patient_id', $user->patient?->id));
            } elseif ($user->isManager() || $user->isLabStaff()) {
                $query->whereHas(
                    'labRequestItem.visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment',
                    fn ($facilityQuery) => $facilityQuery->whereIn('facility_id', $user->accessibleFacilityIds())
                );
            } else {
                $query->whereKey(-1);
            }
        }

        return $this->paginateList($query, $filters, ['value', 'unit'], ['labRequestItem.labTest' => ['name'], 'labRequestItem.visit.patient.profile' => ['full_name'], 'labStaff.employee.profile' => ['full_name']]);
    }

    public function find(int $id): LabResult
    {
        return LabResult::with([
            'labRequestItem.labTest',
            'labRequestItem.visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'labStaff.employee.profile',
            'labStaff.employee.facility',
        ])->findOrFail($id);
    }

    public function create(array $data): LabResult
    {
        return LabResult::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $labResult = LabResult::findOrFail($id);
        return $labResult->update($data);
    }

    public function delete(int $id): bool
    {
        $labResult = LabResult::findOrFail($id);
        return $labResult->delete();
    }

    public function existsForRequest(int $labRequestItemId): bool
    {
        return LabResult::where(
            'lab_request_item_id',
            $labRequestItemId
        )->exists();
    }

    public function findByRequest(int $labRequestItemId): ?LabResult
    {
        return LabResult::where(
            'lab_request_item_id',
            $labRequestItemId
        )->first();
    }
}

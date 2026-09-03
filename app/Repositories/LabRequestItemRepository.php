<?php

namespace App\Repositories;

use App\Models\LabRequestItem;
use App\Models\User;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class LabRequestItemRepository
{
    use ListQuery;

    public function all(array $filters = [], ?User $user = null): LengthAwarePaginator
    {
        $query = LabRequestItem::with(['visit', 'labTest']);

        if ($user !== null && !$user->isAdmin()) {
            if ($user->isDoctor()) {
                $query->whereHas('visit', fn ($visitQuery) => $visitQuery->where('doctor_id', $user->doctor?->id));
            } elseif ($user->isPatient()) {
                $query->whereHas('visit', fn ($visitQuery) => $visitQuery->where('patient_id', $user->patient?->id));
            } elseif ($user->isManager() || $user->isLabStaff()) {
                $facilityIds = $user->accessibleFacilityIds();
                $query->whereHas(
                    'visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment',
                    fn ($facilityQuery) => $facilityQuery->whereIn('facility_id', $facilityIds)
                );
            } else {
                $query->whereKey(-1);
            }
        }

        return $this->paginateList($query, $filters, ['status'], ['labTest' => ['name'], 'visit.patient.profile' => ['full_name'], 'visit.doctor.employee.profile' => ['full_name']]);
    }

    public function find(int $id): LabRequestItem
    {
        return LabRequestItem::with(['visit', 'labTest'])->findOrFail($id);
    }

    public function create(array $data): LabRequestItem
    {
        return LabRequestItem::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $labRequestItem = LabRequestItem::findOrFail($id);
        return $labRequestItem->update($data);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return LabRequestItem::whereKey($id)->update(['status' => $status]) > 0;
    }

    public function delete(int $id): bool
    {
        $labRequestItem = LabRequestItem::findOrFail($id);
        return $labRequestItem->delete();
    }

    public function existsForVisit(int $visitId, int $labTestId): bool
    {
        return LabRequestItem::where(
            'visit_id',
            $visitId
        )
            ->where(
                'lab_test_id',
                $labTestId
            )
            ->exists();
    }

    public function hasResult(int $id): bool
    {
        return LabRequestItem::whereKey($id)
            ->whereHas('labResult')
            ->exists();
    }

    public function findByVisitAndTest(int $visitId, int $labTestId): ?LabRequestItem
    {
        return LabRequestItem::where(
            'visit_id',
            $visitId
        )
            ->where(
                'lab_test_id',
                $labTestId
            )
            ->first();
    }
}

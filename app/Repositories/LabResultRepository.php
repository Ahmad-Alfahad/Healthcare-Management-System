<?php

namespace App\Repositories;

use App\Models\LabResult;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class LabResultRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(LabResult::with([
            'labRequestItem.visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'labStaff.facility',
        ]), $filters, ['value', 'unit', 'status'], ['labRequestItem.labTest' => ['name'], 'labRequestItem.visit.patient.profile' => ['full_name'], 'labStaff.profile' => ['full_name']]);
    }

    public function find(int $id): LabResult
    {
        return LabResult::with([
            'labRequestItem.visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'labStaff.facility',
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

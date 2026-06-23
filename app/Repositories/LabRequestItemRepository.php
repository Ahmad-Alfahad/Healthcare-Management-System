<?php

namespace App\Repositories;

use App\Models\LabRequestItem;
use Illuminate\Database\Eloquent\Collection;

class LabRequestItemRepository
{
    public function all(): Collection
    {
        return LabRequestItem::with(['visit', 'labTest'])->get();
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

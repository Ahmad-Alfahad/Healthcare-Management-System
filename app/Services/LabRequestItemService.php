<?php

namespace App\Services;

use App\Models\LabRequestItem;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\LabRequestItemRepository;
use App\Repositories\VisitRepository;
use Illuminate\Validation\ValidationException;

class LabRequestItemService
{
    protected LabRequestItemRepository $labRequestItemRepository;
    protected VisitRepository $visitRepository;

    public function __construct(LabRequestItemRepository $labRequestItemRepository, VisitRepository $visitRepository)
    {
        $this->labRequestItemRepository = $labRequestItemRepository;
        $this->visitRepository = $visitRepository;
    }

    public function getAllLabRequestItems(): Collection
    {
        return $this->labRequestItemRepository->all();
    }

    public function getLabRequestItemById(int $id): LabRequestItem
    {
        return $this->labRequestItemRepository->find($id);
    }

    public function createLabRequestItem(array $data): LabRequestItem
    {
        $visit =
            $this->visitRepository->find($data['visit_id']);

        $this->validateVisitIsActive($visit);

        $this->validateDuplicateLabRequest(
            $data['visit_id'],
            $data['lab_test_id']
        );

        $data['requested_at'] = now();

        return $this->labRequestItemRepository->create(
            $data
        );
    }

    public function updateLabRequestItem(int $id, array $data): bool
    {
        return  $this->labRequestItemRepository->update(
            $id,
            $data
        );
    }

    public function deleteLabRequestItem(int $id): bool
    {
        $this->validateLabRequestDeletion(
            $id
        );
        return $this->labRequestItemRepository->delete($id);
    }

    private function validateVisitIsActive(Visit $visit): void
    {
        if ($visit->status !== 'in_progress') {

            throw ValidationException::withMessages([
                'visit_id' => [
                    'Lab tests can only be requested during an active visit.'
                ]
            ]);
        }
    }

    private function validateDuplicateLabRequest(int $visitId, int $labTestId): void
    {
        if (
            $this->labRequestItemRepository->existsForVisit(
                $visitId,
                $labTestId
            )
        ) {
            throw ValidationException::withMessages([
                'lab_test_id' => [
                    'This test has already been requested for the visit.'
                ]
            ]);
        }
    }

    private function validateLabRequestDeletion(int $id): void
    {
        if (
            $this->labRequestItemRepository->hasResult($id)
        ) {
            throw ValidationException::withMessages([
                'lab_request_item_id' => [
                    'Cannot delete a lab request that already has a result.'
                ]
            ]);
        }
    }
}

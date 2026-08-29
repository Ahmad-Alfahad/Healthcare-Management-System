<?php

namespace App\Services;

use App\Models\LabResult;
use App\Models\LabRequestItem;
use App\Models\User;
use App\Repositories\LabResultRepository;
use App\Repositories\LabRequestItemRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;



class LabResultService
{
    protected LabResultRepository $labResultRepository;
    protected LabRequestItemRepository $labRequestItemRepository;

    public function __construct(LabResultRepository $labResultRepository, LabRequestItemRepository $labRequestItemRepository)
    {
        $this->labResultRepository = $labResultRepository;
        $this->labRequestItemRepository = $labRequestItemRepository;
    }

    public function getAllLabResults(array $filters = []): LengthAwarePaginator
    {
        return $this->labResultRepository->all($filters);
    }

    public function getLabResultById(int $id): LabResult
    {
        return $this->labResultRepository->find($id);
    }

    public function createLabResult(array $data, User $user): LabResult
    {
        if (!$user->isLabStaff()) {
            throw ValidationException::withMessages([
                'lab_staff' => [
                    'Authenticated user is not laboratory staff.'
                ]
            ]);
        }

        $labStaff = $user->labStaff;
        if (!$labStaff) {
            throw ValidationException::withMessages([
                'lab_staff' => [
                    'Authenticated user is not assigned to a laboratory staff record.'
                ]
            ]);
        }
        $labRequest =
            $this->labRequestItemRepository
            ->find(
                $data['lab_request_item_id']
            );

        $this->validateResultUniqueness(
            $data['lab_request_item_id']
        );

        $this->validateRequestCanReceiveResult(
            $labRequest
        );

        $data['unit'] = $labRequest->labTest->unit;

        $data['reference_range'] = $labRequest->labTest->range_low . ' - ' . $labRequest->labTest->range_high;

        $data['completed_at'] = now();
        $data['lab_staff_id'] = $labStaff->id;
        return DB::transaction(function () use ($data, $labRequest): LabResult {
            $result = $this->labResultRepository->create($data);
            $this->labRequestItemRepository->updateStatus($labRequest->id, 'completed');

            return $result;
        });
    }

    public function updateLabResult(int $id, array $data): bool
    {
        $labResult = $this->labResultRepository->find($id);

        $this->validateResultIsEditable(
            $labResult
        );

        return $this->labResultRepository->update($id, $data);
    }

    public function deleteLabResult(int $id): bool
    {
        $labResult = $this->labResultRepository->find($id);

        $this->validateResultDeletion(
            $labResult
        );
        return $this->labResultRepository->delete($id);
    }

    private function validateResultUniqueness(int $labRequestItemId): void
    {
        if (
            $this->labResultRepository
            ->existsForRequest(
                $labRequestItemId
            )
        ) {
            throw ValidationException::withMessages([
                'lab_request_item_id' => [
                    'A result already exists for this lab request.'
                ]
            ]);
        }
    }

    private function validateRequestCanReceiveResult(LabRequestItem $request): void
    {
        if ($request->visit->status === 'cancelled') {

            throw ValidationException::withMessages([
                'lab_request_item_id' => [
                    'Cannot add result to a cancelled visit.'
                ]
            ]);
        }

        if ($request->status !== 'processing') {
            throw ValidationException::withMessages([
                'lab_request_item_id' => [
                    'A lab result can only be recorded for a request that is processing.'
                ]
            ]);
        }
    }

    private function validateResultIsEditable(LabResult $labResult): void
    {
        if (
            in_array(
                $labResult->labRequestItem->status,
                ['completed', 'cancelled']
            )
        ) {
            throw ValidationException::withMessages([
                'status' => [
                    'Completed or cancelled results cannot be modified.'
                ]
            ]);
        }
    }

    private function validateResultDeletion(LabResult $labResult): void
    {
        if (
            in_array(
                $labResult->labRequestItem->status,
                ['completed', 'cancelled']
            )
        ) {
            throw ValidationException::withMessages([
                'lab_result_id' => [
                    'Completed or cancelled results cannot be deleted.'
                ]
            ]);
        }
    }
}

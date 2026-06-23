<?php

namespace App\Services;

use App\Models\LabResult;
use App\Models\LabRequestItem;
use App\Repositories\LabResultRepository;
use App\Repositories\LabRequestItemRepository;
use Illuminate\Database\Eloquent\Collection;
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

    public function getAllLabResults(): Collection
    {
        return $this->labResultRepository->all();
    }

    public function getLabResultById(int $id): LabResult
    {
        return $this->labResultRepository->find($id);
    }

    public function createLabResult(array $data): LabResult
    {
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

        $data['status'] = 'pending';

        $data['unit'] = $labRequest->labTest->unit;

        $data['reference_range'] = $labRequest->labTest->range_low . ' - ' . $labRequest->labTest->range_high;

        $data['completed_at'] = null;

        return $this->labResultRepository->create($data);
    }

    public function updateLabResult(int $id, array $data): bool
    {
        $labResult = $this->labResultRepository->find($id);

        $this->validateResultIsEditable(
            $labResult
        );

        if (isset($data['status'])) {

            $this->validateStatusTransition(
                $labResult->status,
                $data['status']
            );
        }
        $this->validateCompletedResult(
            $data
        );
        if (
            ($data['status'] ?? null)
            === 'completed'
        ) {
            $data['completed_at'] = now();
        }
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
    }

    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $allowedTransitions = [
            'pending' => [
                'processing',
                'cancelled'
            ],

            'processing' => [
                'completed'
            ],

            'completed' => [],

            'cancelled' => [],
        ];

        if (
            !in_array(
                $newStatus,
                $allowedTransitions[$currentStatus]
            )
        ) {
            throw ValidationException::withMessages([
                'status' => [
                    "Invalid status transition from {$currentStatus} to {$newStatus}."
                ]
            ]);
        }
    }

    private function validateCompletedResult(array $data): void
    {
        if (
            ($data['status'] ?? null) === 'completed'
            && empty($data['value'])
        ) {
            throw ValidationException::withMessages([
                'value' => [
                    'Result value is required before completion.'
                ]
            ]);
        }
    }

    private function validateResultIsEditable(LabResult $labResult): void
    {
        if (
            in_array(
                $labResult->status,
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
                $labResult->status,
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

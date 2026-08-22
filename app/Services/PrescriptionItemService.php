<?php

namespace App\Services;

use App\Models\PrescriptionItem;
use App\Models\Prescription;
use App\Repositories\PrescriptionItemRepository;
use App\Repositories\PrescriptionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class PrescriptionItemService
{
    protected PrescriptionItemRepository  $prescriptionItemRepository;
    protected PrescriptionRepository  $prescriptionRepository;

    public function __construct(PrescriptionItemRepository $prescriptionItemRepository, PrescriptionRepository $prescriptionRepository)
    {
        $this->prescriptionItemRepository = $prescriptionItemRepository;
        $this->prescriptionRepository = $prescriptionRepository;
    }

    public function getAllPrescriptionItems(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->prescriptionItemRepository->all($user, $filters);
    }

    public function getPrescriptionItemById(int $id): PrescriptionItem
    {
        return $this->prescriptionItemRepository->find($id);
    }

    public function createPrescriptionItem(array $data): PrescriptionItem
    {
        $prescription = $this->prescriptionRepository
            ->find($data['prescription_id']);

        $this->validatePrescriptionAllowsItemModification(
            $prescription
        );
        $this->validateMedicationUniqueness(
            $data['prescription_id'],
            $data['medication_name']
        );
        return $this->prescriptionItemRepository->create($data);
    }

    public function updatePrescriptionItem(int $id, array $data): bool
    {
        $item = $this->prescriptionItemRepository->find($id);

        $prescription = $this->prescriptionRepository
            ->find($item->prescription_id);

        $this->validatePrescriptionAllowsItemModification(
            $prescription
        );

        if (array_key_exists('medication_name', $data)) {
            $this->validateMedicationUniquenessUpdate(
                $item->prescription_id,
                $data['medication_name'],
                $id
            );
        }

        return $this->prescriptionItemRepository->update($id, $data);
    }

    public function deletePrescriptionItem(int $id): bool
    {
        $item = $this->prescriptionItemRepository
            ->find($id);

        $prescription = $this->prescriptionRepository
            ->find($item->prescription_id);

        $this->validatePrescriptionAllowsItemModification(
            $prescription
        );
        return $this->prescriptionItemRepository->delete($id);
    }

    private function validateMedicationUniqueness(int $prescriptionId, string $medicationName): void
    {
        if (
            $this->prescriptionItemRepository
            ->existsMedicationInPrescription(
                $prescriptionId,
                $medicationName
            )
        ) {
            throw ValidationException::withMessages([
                'medication_name' => [
                    'This medication already exists in the prescription.'
                ]
            ]);
        }
    }

    private function validateMedicationUniquenessUpdate(int $prescriptionId, string $medicationName, int $itemId): void
    {
        if (
            $this->prescriptionItemRepository
            ->existsMedicationInPrescriptionExcept(
                $prescriptionId,
                $medicationName,
                $itemId
            )
        ) {
            throw ValidationException::withMessages([
                'medication_name' => [
                    'This medication already exists in the prescription.'
                ]
            ]);
        }
    }

    private function validatePrescriptionAllowsItemModification(Prescription $prescription): void
    {
        if ($prescription->status !== 'pending') {
            throw ValidationException::withMessages([
                'prescription_id' => [
                    'Prescription items can only be modified while the prescription is pending.'
                ]
            ]);
        }
    }
}

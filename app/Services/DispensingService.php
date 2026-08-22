<?php

namespace App\Services;

use App\Models\Dispensing;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Repositories\DispensingRepository;
use App\Repositories\PrescriptionItemRepository;
use App\Repositories\PrescriptionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use App\Models\User;


class DispensingService
{
    protected DispensingRepository  $dispensingRepository;
    protected PrescriptionItemRepository  $prescriptionItemRepository;
    protected PrescriptionRepository  $prescriptionRepository;

    public function __construct(
        DispensingRepository $dispensingRepository,
        PrescriptionItemRepository $prescriptionItemRepository,
        PrescriptionRepository $prescriptionRepository
    ) {
        $this->dispensingRepository = $dispensingRepository;
        $this->prescriptionItemRepository = $prescriptionItemRepository;
        $this->prescriptionRepository = $prescriptionRepository;
    }

    public function getAllDispensings(User $user): Collection
    {
        return $this->dispensingRepository->all($user);
    }

    public function getDispensingById(int $id): Dispensing
    {
        return $this->dispensingRepository->find($id);
    }

    public function createDispensing(array $data): Dispensing
    {
        $item = $this->prescriptionItemRepository
            ->find($data['prescription_item_id']);
        $prescription = $this->prescriptionRepository
            ->find($item->prescription_id);
        $this->validatePrescriptionAllowsDispensing(
            $prescription
        );
        $this->validateDispensedQuantity(
            $item,
            $data['quantity_dispensed']
        );
        $dispensing = $this->dispensingRepository
            ->create($data);
        $this->updatePrescriptionStatusAfterDispensing(
            $prescription->id
        );
        return $dispensing;
    }

    public function updateDispensing(int $id, array $data): bool
    {
        return $this->dispensingRepository->update($id, $data);
    }

    public function deleteDispensing(int $id): bool
    {
        return $this->dispensingRepository->delete($id);
    }

    private function validatePrescriptionAllowsDispensing(Prescription $prescription): void
    {
        if (
            in_array(
                $prescription->status,
                ['dispensed', 'cancelled']
            )
        ) {
            throw ValidationException::withMessages([
                'prescription_id' => [
                    'This prescription cannot be dispensed.'
                ]
            ]);
        }
    }

    private function validateDispensedQuantity(PrescriptionItem $item, int $requestedQuantity): void
    {
        $alreadyDispensed = $this->dispensingRepository
            ->getTotalDispensedForItem(
                $item->id
            );

        $newTotal =
            $alreadyDispensed +
            $requestedQuantity;

        if (
            $newTotal >
            $item->quantity_prescribed
        ) {
            throw ValidationException::withMessages([
                'quantity_dispensed' => [
                    'Dispensed quantity exceeds prescribed quantity.'
                ]
            ]);
        }
    }


    private function updatePrescriptionStatusAfterDispensing(int $prescriptionId): void
    {
        $prescription = $this->prescriptionRepository
            ->find($prescriptionId);

        $items = $prescription->items;

        $allDispensed = true;
        $hasDispensed = false;

        foreach ($items as $item) {

            $dispensed = $this->dispensingRepository
                ->getTotalDispensedForItem(
                    $item->id
                );

            if ($dispensed > 0) {
                $hasDispensed = true;
            }

            if (
                $dispensed <
                $item->quantity_prescribed
            ) {
                $allDispensed = false;
            }
        }

        if ($allDispensed) {

            $this->prescriptionRepository
                ->updateStatus(
                    $prescriptionId,
                    'dispensed'
                );

            return;
        }

        if ($hasDispensed) {

            $this->prescriptionRepository
                ->updateStatus(
                    $prescriptionId,
                    'partial'
                );
        }
    }
}

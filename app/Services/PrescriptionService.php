<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\PrescriptionRepository;
use App\Repositories\VisitRepository;
use App\Models\User;
use Illuminate\Validation\ValidationException;


class PrescriptionService
{
    protected  PrescriptionRepository $prescriptionRepository;
    protected  VisitRepository $visitRepository;

    public function __construct(PrescriptionRepository $prescriptionRepository, VisitRepository $visitRepository)
    {
        $this->prescriptionRepository = $prescriptionRepository;
        $this->visitRepository = $visitRepository;
    }

    public function getAllPrescriptions(User $user): Collection
    {
        return $this->prescriptionRepository->all($user);
    }

    public function getPrescriptionById(int $id): Prescription
    {
        return $this->prescriptionRepository->find($id);
    }

    public function createPrescription(array $data): Prescription
    {
        $visit = $this->visitRepository
            ->find($data['visit_id']);
        $this->validateVisitAllowsPrescriptionCreation(
            $visit
        );

        $this->validatePrescriptionUniqueness(
            $data['visit_id']
        );
        $data['status'] = 'pending';
        return $this->prescriptionRepository->create($data);
    }

    public function updatePrescription(int $id, array $data): bool
    {
        $prescription = $this->prescriptionRepository
            ->find($id);

        $visit = $this->visitRepository
            ->find($prescription->visit_id);

        $this->validateVisitAllowsPrescriptionUpdate(
            $visit
        );
        return $this->prescriptionRepository->update($id, $data);
    }

    public function deletePrescription(int $id): bool
    {

        $prescription = $this->prescriptionRepository
            ->find($id);

        $visit = $this->visitRepository
            ->find($prescription->visit_id);

        $this->validateVisitAllowsPrescriptionDeletion(
            $visit
        );
        return $this->prescriptionRepository->delete($id);
    }

    private function validatePrescriptionUniqueness(int $visitId): void
    {
        if (
            $this->prescriptionRepository
            ->existsByVisitId($visitId)
        ) {
            throw ValidationException::withMessages([
                'visit_id' => [
                    'This visit already has a prescription.'
                ]
            ]);
        }
    }

    private function validateVisitAllowsPrescriptionCreation(Visit $visit): void
    {
        if ($visit->status !== 'in_progress') {

            throw ValidationException::withMessages([
                'visit_id' => [
                    'Prescription can only be created during an active visit.'
                ]
            ]);
        }
    }

    private function validateVisitAllowsPrescriptionUpdate(Visit $visit): void
    {
        if ($visit->status === 'cancelled') {

            throw ValidationException::withMessages([
                'visit_id' => [
                    'Prescription cannot be modified for a cancelled visit.'
                ]
            ]);
        }
    }

    private function validateVisitAllowsPrescriptionDeletion(Visit $visit): void
    {
        if ($visit->status !== 'in_progress') {

            throw ValidationException::withMessages([
                'visit_id' => [
                    'Prescription can only be deleted during an active visit.'
                ]
            ]);
        }
    }

    public function cancelPrescription(int $prescriptionId): bool
    {
        $prescription = $this->prescriptionRepository
            ->find($prescriptionId);

        if ($prescription->status !== 'pending') {

            throw ValidationException::withMessages([
                'status' => [
                    'Only pending prescriptions can be cancelled.'
                ]
            ]);
        }

        return $this->prescriptionRepository
            ->update(
                $prescriptionId,
                [
                    'status' => 'cancelled'
                ]
            );
    }
}

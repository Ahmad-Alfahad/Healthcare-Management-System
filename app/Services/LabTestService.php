<?php

namespace App\Services;

use App\Models\LabTest;
use App\Repositories\LabTestRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class LabTestService
{
    protected LabTestRepository $labTestRepository;

    public function __construct(LabTestRepository $labTestRepository)
    {
        $this->labTestRepository = $labTestRepository;
    }

    public function getAllTests(array $filters = [])
    {
        return $this->labTestRepository->all($filters);
    }

    public function getTestById(int $id): LabTest
    {
        return $this->labTestRepository->find($id);
    }

    public function createTest(array $data): LabTest
    {
        $this->validateLabTestUniqueness(
            $data['name']
        );

        $this->validateReferenceRange(
            $data['range_low'],
            $data['range_high']
        );
        return $this->labTestRepository->create($data);
    }

    public function updateTest(int $id, array $data): bool
    {
        $labTest =
            $this->labTestRepository->find($id);

        $this->validateLabTestUniquenessUpdate(
            $id,
            $data['name']
        );

        $this->validateReferenceRange(
            $data['range_low'],
            $data['range_high']
        );

        return $this->labTestRepository->update($id, $data);
    }

    public function deleteTest(int $id): bool
    {
        $this->validateLabTestDeletion(
            $id
        );
        return $this->labTestRepository->delete($id);
    }

    private function validateLabTestUniqueness(string $name): void
    {
        if (
            $this->labTestRepository
            ->existsByName($name)
        ) {
            throw ValidationException::withMessages([
                'name' => [
                    'Lab test already exists.'
                ]
            ]);
        }
    }

    private function validateLabTestUniquenessUpdate(int $id, string $name): void
    {
        if (
            $this->labTestRepository
            ->existsByNameExcept(
                $name,
                $id
            )
        ) {
            throw ValidationException::withMessages([
                'name' => [
                    'Lab test already exists.'
                ]
            ]);
        }
    }

    private function validateReferenceRange(int|float $rangeLow, int|float $rangeHigh): void
    {
        if ($rangeLow > $rangeHigh) {

            throw ValidationException::withMessages([
                'range_low' => [
                    'Range low cannot be greater than range high.'
                ]
            ]);
        }
    }


    private function validateLabTestDeletion(int $id): void
    {
        if (
            $this->labTestRepository
            ->hasRequests($id)
        ) {
            throw ValidationException::withMessages([
                'lab_test_id' => [
                    'Cannot delete lab test because it is already used.'
                ]
            ]);
        }
    }
}

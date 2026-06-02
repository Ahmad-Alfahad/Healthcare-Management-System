<?php
namespace App\Services;

use App\Models\LabTest;
use App\Repositories\LabTestRepository;
use Illuminate\Database\Eloquent\Collection;

class LabTestService
{
    protected $labTestRepository;

    public function __construct(LabTestRepository $labTestRepository)
    {
        $this->labTestRepository = $labTestRepository;
    }

    public function getAllTests(): Collection
    {
        return $this->labTestRepository->all();
    }

    public function getTestById(int $id): LabTest
    {
        return $this->labTestRepository->find($id);
    }

    public function createTest(array $data): LabTest
    {
        return $this->labTestRepository->create($data);
    }

    public function updateTest(int $id, array $data): bool
    {
        return $this->labTestRepository->update($id, $data);
    }

    public function deleteTest(int $id): bool
    {
        return $this->labTestRepository->delete($id);
    }
}
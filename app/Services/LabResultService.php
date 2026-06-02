<?php
namespace App\Services;

use App\Models\LabResult;
use App\Repositories\LabResultRepository;
use Illuminate\Database\Eloquent\Collection;

class LabResultService
{
    protected $labResultRepository;

    public function __construct(LabResultRepository $labResultRepository)
    {
        $this->labResultRepository = $labResultRepository;
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
        return $this->labResultRepository->create($data);
    }

    public function updateLabResult(LabResult $labResult, array $data): bool
    {
        return $this->labResultRepository->update($labResult, $data);
    }

    public function deleteLabResult(LabResult $labResult): bool
    {
        return $this->labResultRepository->delete($labResult);
    }
}
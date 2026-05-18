<?php
namespace App\Services;

use App\Repositories\LabStaffRepository;
use App\Models\LabStaff;
use Illuminate\Database\Eloquent\Collection;

class LabStaffService
{
    protected $labStaffRepository;

    public function __construct(LabStaffRepository $labStaffRepository)
    {
        $this->labStaffRepository = $labStaffRepository;
    }

    public function getAllStaff(): Collection
    {
        return $this->labStaffRepository->all();
    }

    public function getStaffById(int $id): LabStaff
    {
        return $this->labStaffRepository->find($id);
    }

    public function createStaff(array $data): LabStaff
    {
        return $this->labStaffRepository->create($data);
    }

    public function updateStaff(int $id, array $data): bool
    {
        return $this->labStaffRepository->update($id, $data);
    }

    public function deleteStaff(int $id): bool
    {
        return $this->labStaffRepository->delete($id);
    }
}
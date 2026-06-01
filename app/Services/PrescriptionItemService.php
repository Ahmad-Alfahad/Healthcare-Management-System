<?php

namespace App\Services;

use App\Models\PrescriptionItem;
use App\Repositories\PrescriptionItemRepository;
use Illuminate\Database\Eloquent\Collection;

class PrescriptionItemService
{
    protected $repository;

    public function __construct(PrescriptionItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllPrescriptionItems(): Collection
    {
        return $this->repository->all();
    }

    public function getPrescriptionItemById(int $id): PrescriptionItem
    {
        return $this->repository->find($id);
    }

    public function createPrescriptionItem(array $data): PrescriptionItem
    {
        return $this->repository->create($data);
    }

    public function updatePrescriptionItem(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deletePrescriptionItem(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
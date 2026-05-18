<?php

namespace App\Services;

use App\Repositories\PharmacistRepository;
use App\Models\Pharmacist;
use Illuminate\Database\Eloquent\Collection;

class PharmacistService
{
    protected $pharmacistRepository;

    public function __construct(PharmacistRepository $pharmacistRepository)
    {
        $this->pharmacistRepository = $pharmacistRepository;
    }

    public function getAllPharmacists(): Collection
    {
        return $this->pharmacistRepository->all();
    }

    public function getPharmacistById(int $id): Pharmacist
    {
        return $this->pharmacistRepository->find($id);
    }

    public function createPharmacist(array $data): Pharmacist
    {
        return $this->pharmacistRepository->create($data);
    }

    public function updatePharmacist(int $id, array $data): bool
    {
        return $this->pharmacistRepository->update($id, $data);
    }

    public function deletePharmacist(int $id): bool
    {
        return $this->pharmacistRepository->delete($id);
    }
}
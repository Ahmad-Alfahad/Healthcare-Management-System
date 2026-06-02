<?php
namespace App\Services;

use App\Models\Dispensing;
use App\Repositories\DispensingRepository;
use Illuminate\Database\Eloquent\Collection;

class DispensingService
{
    protected $dispensingRepository;

    public function __construct(DispensingRepository $dispensingRepository)
    {
        $this->dispensingRepository = $dispensingRepository;
    }

    public function getAllDispensings(): Collection
    {
        return $this->dispensingRepository->all();
    }

    public function getDispensingById(int $id): Dispensing
    {
        return $this->dispensingRepository->find($id);
    }

    public function createDispensing(array $data): Dispensing
    {
        return $this->dispensingRepository->create($data);
    }

    public function updateDispensing(int $id, array $data): bool
    {
        return $this->dispensingRepository->update($id, $data);
    }

    public function deleteDispensing(int $id): bool
    {
        return $this->dispensingRepository->delete($id);
    }
}
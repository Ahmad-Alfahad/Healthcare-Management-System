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

    public function all(): Collection
    {
        return $this->dispensingRepository->all();
    }

    public function find(int $id): Dispensing
    {
        return $this->dispensingRepository->find($id);
    }

    public function create(array $data): Dispensing
    {
        return $this->dispensingRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->dispensingRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->dispensingRepository->delete($id);
    }
}
<?php

namespace App\Services;

use App\Models\Visit;
use App\Repositories\VisitRepository;
use Illuminate\Database\Eloquent\Collection;

class VisitService
{
    protected $visitRepository;
    public function __construct( VisitRepository $visitRepository)
    {
        $this->visitRepository = $visitRepository;
    }

    public function getAllVisits(): Collection
    {
        return $this->visitRepository->all();
    }

    public function getVisitById(int $id): Visit
    {
        return $this->visitRepository->find($id);
    }

    public function createVisit(array $data): Visit
    {
        return $this->visitRepository->create($data);
    }

    public function updateVisit(int $id, array $data): bool
    {
        return $this->visitRepository->update($id, $data);
    }

    public function deleteVisit(int $id): bool
    {
        return $this->visitRepository->delete($id);
    }

}
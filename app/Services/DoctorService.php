<?php

namespace App\Services;

use App\Repositories\DoctorRepository;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Collection;

class DoctorService
{
    protected $doctorRepository;

    public function __construct(DoctorRepository $doctorRepository)
    {
        $this->doctorRepository = $doctorRepository;
    }

    public function getAllDoctors(): Collection
    {
        return $this->doctorRepository->all();
    }

    public function getDoctorById(int $id): Doctor
    {
        return $this->doctorRepository->find($id);
    }

    public function createDoctor(array $data): Doctor
    {
        return $this->doctorRepository->create($data);
    }

    public function updateDoctor(int $id, array $data): bool
    {
        return $this->doctorRepository->update($id, $data);
    }

    public function deleteDoctor(int $id): bool
    {
        return $this->doctorRepository->delete($id);
    }
}
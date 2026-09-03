<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Facility;
use App\Models\Profile;
use App\Models\Doctor;
use App\Models\Pharmacist;
use App\Models\LabStaff;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\User;
use App\Repositories\EmployeeRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class EmployeeService
{
    public function __construct(
        protected EmployeeRepository $employeeRepository,
        protected DatabaseManager $db
    ) {
    }

    public function getAllEmployees(
        User $user,
        array $filters = []
    ) {
        if ($user->isAdmin()) {
            return $this->employeeRepository->all($filters);
        }

        if ($user->isManager()) {
            $facility = $user->facility();

            if (!$facility) {
                return new Collection();
            }

            return $this->employeeRepository->getByFacility(
                $user->accessibleFacilityIds(),
                $filters
            );
        }

        return $this->employeeRepository->all($filters);
    }

    public function getEmployeeById(int $id): Employee
    {
        return $this->employeeRepository->find($id);
    }

    public function createEmployee(array $data): Employee
    {
        return $this->db->transaction(function () use ($data): Employee {
            $facility = Facility::findOrFail($data['facility_id']);
            $this->validateFacility($facility);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => $data['is_active'] ?? true,
            ]);
            $user->assignRole($data['role']);

            $profile = Profile::create([
                'user_id' => $user->id,
                'full_name' => $data['full_name'],
                'national_number' => $data['national_number'] ?? null,
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
            ]);

            $employee = $this->employeeRepository->create([
                'profile_id' => $profile->id,
                'facility_id' => $data['facility_id'],
                'languages' => $data['languages'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if ($data['role'] === 'doctor') {
                $this->validateDoctorAssignment(
                    (int) $data['facility_department_specialization_id'],
                    $facility
                );
            }

            $this->createProfessionalRecord($employee, $data);

            return $this->employeeRepository->find($employee->id);
        });
    }

    public function updateEmployee(int $id, array $data): bool
    {
        $employee = $this->employeeRepository->find($id);

        if (isset($data['profile_id'])) {
            $this->validateProfileNotAssigned(
                (int) $data['profile_id'],
                $employee->id
            );
        }

        if (isset($data['facility_id'])) {
            $facility = Facility::findOrFail($data['facility_id']);
            $this->validateFacility($facility);
        }

        $updated = $this->db->transaction(function () use ($id, $data, $employee): bool {
            $updated = $this->employeeRepository->update($id, $data);

            if (array_key_exists('is_active', $data)) {
                $employee->loadMissing('profile.user');
                $employee->profile?->user?->update([
                    'is_active' => (bool) $data['is_active'],
                ]);
            }

            return $updated;
        });

        return $updated;
    }

    public function deleteEmployee(int $id): bool
    {
        return $this->db->transaction(function () use ($id): bool {
            $employee = $this->employeeRepository->find($id);
            $profile = $employee->profile;
            $user = $profile?->user;
            $deleted = $this->employeeRepository->delete($id);

            if ($deleted && $profile && !$profile->patient()->exists()) {
                $profile->delete();
                $user?->delete();
            }

            return $deleted;
        });
    }

    private function createProfessionalRecord(Employee $employee, array $data): void
    {
        $common = [
            'years_of_experience' => $data['years_of_experience'] ?? 0,
        ];

        match ($data['role']) {
            'doctor' => Doctor::create($common + [
                'employee_id' => $employee->id,
                'facility_department_specialization_id' => $data['facility_department_specialization_id'],
                'qualification' => $data['qualification'],
                'biography' => $data['biography'] ?? null,
                'achievements' => $data['achievements'] ?? null,
            ]),
            'pharmacist' => Pharmacist::create($common + [
                'employee_id' => $employee->id,
                'degree' => $data['degree'],
                'license_number' => $data['license_number'] ?? null,
            ]),
            'laboratory' => LabStaff::create($common + [
                'employee_id' => $employee->id,
                'specialization' => $data['specialization'],
                'degree' => $data['degree'],
                'license_number' => $data['license_number'] ?? null,
            ]),
        };
    }

    private function validateProfileNotAssigned(
        int $profileId,
        ?int $ignoreEmployeeId = null
    ): void {
        $query = Employee::where('profile_id', $profileId);

        if ($ignoreEmployeeId !== null) {
            $query->where('id', '!=', $ignoreEmployeeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'profile_id' => [
                    'This profile is already assigned to another employee.',
                ],
            ]);
        }
    }

    private function validateFacility(Facility $facility): void
    {
        if (!$facility->is_active) {
            throw ValidationException::withMessages([
                'facility_id' => [
                    'The selected facility is inactive.',
                ],
            ]);
        }
    }

    private function validateDoctorAssignment(
        int $assignmentId,
        Facility $facility
    ): void {
        $assignment = FacilityDepartmentSpecialization::with(
            'facilityDepartment'
        )->find($assignmentId);

        if (
            !$assignment
            || !$assignment->is_active
            || $assignment->facilityDepartment->facility_id !== $facility->id
        ) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'The selected doctor assignment does not belong to the employee facility or is inactive.',
                ],
            ]);
        }
    }
}

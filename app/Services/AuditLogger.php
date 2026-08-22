<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\Profile;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Visit;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Dispensing;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    private const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'remember_token',
        'api_token',
        'access_token',
        'refresh_token',
    ];

    public function record(
        string $action,
        Model $model,
        ?array $oldValue = null,
        ?array $newValue = null
    ): void {
        $userId = Auth::id();

        if ($userId === null || $model instanceof AuditLog) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => $userId,
            'facility_id' => $this->resolveFacilityId($model),
            'table_name' => $model->getTable(),
            'action' => $action,
            'record_id' => $model->getKey(),
            'old_value' => $this->sanitize($oldValue),
            'new_value' => $this->sanitize($newValue),
        ]);
    }

    private function sanitize(?array $value): ?array
    {
        if ($value === null) {
            return null;
        }

        foreach (self::SENSITIVE_KEYS as $key) {
            unset($value[$key]);
        }

        return $value;
    }

    private function resolveFacilityId(Model $model): ?int
    {
        if ($model instanceof Facility) {
            return $model->getKey();
        }

        if ($model->getAttribute('facility_id')) {
            return (int) $model->getAttribute('facility_id');
        }

        $facility = match (true) {
            $model instanceof User => $model->facility(),
            $model instanceof Profile => $model->user?->facility(),
            $model instanceof Patient => $model->appointments()->with('doctor.facilityDepartmentSpecialization.facilityDepartment.facility')->first()?->doctor?->facilityDepartmentSpecialization?->facilityDepartment?->facility,
            $model instanceof Doctor => $model->facilityDepartmentSpecialization?->facilityDepartment?->facility,
            $model instanceof Appointment => $model->doctor?->facilityDepartmentSpecialization?->facilityDepartment?->facility,
            $model instanceof Visit => $model->doctor?->facilityDepartmentSpecialization?->facilityDepartment?->facility,
            $model instanceof Prescription => $model->visit?->doctor?->facilityDepartmentSpecialization?->facilityDepartment?->facility,
            $model instanceof PrescriptionItem => $model->prescription?->visit?->doctor?->facilityDepartmentSpecialization?->facilityDepartment?->facility,
            $model instanceof Dispensing => $model->prescriptionItem?->prescription?->visit?->doctor?->facilityDepartmentSpecialization?->facilityDepartment?->facility,
            default => null,
        };

        return $facility?->getKey();
    }
}

<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Facility;
use App\Models\LabStaff;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Pharmacist;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DashboardRepository
{
    public function summaryFor(User $user, string $period = 'current_month'): array
    {
        [$from, $to] = $this->periodBounds($period);

        if ($user->isAdmin()) {
            return $this->managementSummary(null, $from, $to);
        }

        if ($user->isManager()) {
            $facilityId = $user->facility()?->id;

            return $facilityId
                ? $this->managementSummary($facilityId, $from, $to)
                : $this->emptySummary();
        }

        if ($user->isPatient()) {
            return $this->appointmentSummary(
                Appointment::query()->where('patient_id', $user->patient?->id),
                Visit::query()->where('patient_id', $user->patient?->id),
                $from,
                $to
            );
        }

        if ($user->isDoctor()) {
            return $this->appointmentSummary(
                Appointment::query()->where('doctor_id', $user->doctor?->id),
                Visit::query()->where('doctor_id', $user->doctor?->id),
                $from,
                $to
            );
        }

        if ($user->isPharmacist()) {
            return [
                'pending_prescriptions' => Prescription::whereIn('status', ['pending', 'partial'])
                    ->whereHas(
                        'visit.doctor.facilityDepartmentSpecialization.facilityDepartment',
                        fn(Builder $query) => $query->where('facility_id', $user->facility()?->id)
                    )
                    ->count(),
            ];
        }

        if ($user->isLabStaff()) {
            return [
                'pending_results' => LabResult::where('lab_staff_id', $user->labStaff?->id)
                    ->whereIn('status', ['pending', 'processing'])
                    ->count(),
            ];
        }

        return [];
    }

    private function emptySummary(): array
    {
        return [
            'facilities' => 0,
            'doctors' => 0,
            'pharmacists' => 0,
            'lab_staff' => 0,
            'patients' => 0,
            'total_appointments' => 0,
            'today_appointments' => 0,
            'upcoming_appointments' => 0,
            'total_visits' => 0,
        ];
    }

    private function managementSummary(?int $facilityId, ?Carbon $from, ?Carbon $to): array
    {
        $facilityQuery = Facility::query();

        if ($facilityId) {
            $facilityQuery->whereKey($facilityId);
        }

        $appointmentQuery = Appointment::query()->whereHas(
            'doctor.facilityDepartmentSpecialization.facilityDepartment',
            fn(Builder $query) => $facilityId
                ? $query->where('facility_id', $facilityId)
                : $query
        );
        $visitQuery = Visit::query()->whereHas(
            'doctor.facilityDepartmentSpecialization.facilityDepartment',
            fn(Builder $query) => $facilityId
                ? $query->where('facility_id', $facilityId)
                : $query
        );

        return [
            'facilities' => $facilityQuery->count(),
            'doctors' => $this->doctorQuery($facilityId)->where('is_active', true)->count(),
            'pharmacists' => Pharmacist::query()
                ->when($facilityId, fn(Builder $query) => $query->where('facility_id', $facilityId))
                ->where('is_active', true)
                ->count(),
            'lab_staff' => LabStaff::query()
                ->when($facilityId, fn(Builder $query) => $query->where('facility_id', $facilityId))
                ->where('is_active', true)
                ->count(),
            'patients' => $this->patientQuery($facilityId)->count(),
            ...$this->appointmentSummary($appointmentQuery, $visitQuery, $from, $to),
        ];
    }

    private function appointmentSummary($query, $visitQuery, ?Carbon $from, ?Carbon $to): array
    {
        $today = Carbon::today();
        $activeStatuses = ['pending', 'confirmed'];

        if ($from && $to) {
            $query->whereBetween('scheduled_date', [$from->toDateString(), $to->toDateString()]);
            $visitQuery->whereBetween('visited_at', [$from->startOfDay(), $to->endOfDay()]);
        }

        return [
            'total_appointments' => (clone $query)->count(),
            'total_visits' => (clone $visitQuery)->count(),
            'today_appointments' => (clone $query)
                ->whereDate('scheduled_date', $today)
                ->whereIn('status', $activeStatuses)
                ->count(),
            'upcoming_appointments' => (clone $query)
                ->whereDate('scheduled_date', '>', $today)
                ->whereIn('status', $activeStatuses)
                ->count(),
        ];
    }

    private function doctorQuery(?int $facilityId = null): Builder
    {
        return Doctor::query()
            ->when(
                $facilityId,
                fn(Builder $query) => $query->whereHas(
                    'facilityDepartmentSpecialization.facilityDepartment',
                    fn(Builder $facilityQuery) => $facilityQuery->where('facility_id', $facilityId)
                )
            );
    }

    private function patientQuery(?int $facilityId = null): Builder
    {
        return Patient::query()->when(
            $facilityId,
            fn(Builder $query) => $query->whereHas(
                'appointments.doctor.facilityDepartmentSpecialization.facilityDepartment',
                fn(Builder $facilityQuery) => $facilityQuery->where('facility_id', $facilityId)
            )
        );
    }

    private function periodBounds(string $period): array
    {
        if ($period === 'all') {
            return [null, null];
        }

        $month = $period === 'last_month'
            ? Carbon::now()->subMonth()
            : Carbon::now();

        return [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()];
    }
}

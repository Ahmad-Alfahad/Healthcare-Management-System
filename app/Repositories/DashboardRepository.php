<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Facility;
use App\Models\LabStaff;
use App\Models\Patient;
use App\Models\Pharmacist;
use App\Models\Prescription;
use App\Models\Dispensing;
use App\Models\LabRequestItem;
use App\Models\LabResult;
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
            return $this->managementSummary(Facility::pluck('id')->all(), $from, $to);
        }

        if ($user->isManager()) {
            $facilityIds = $user->accessibleFacilityIds();

            return $facilityIds
                ? $this->managementSummary($facilityIds, $from, $to)
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
    $facilityIds = $user->accessibleFacilityIds();

    $baseQuery = Prescription::query()
        ->whereHas(
            'visit.doctor.facilityDepartmentSpecialization.facilityDepartment',
            fn (Builder $query) =>
                $query->whereIn('facility_id', $facilityIds)
        );

    return [
        'pending_prescriptions' => (clone $baseQuery)
            ->whereIn('status', ['pending', 'partial'])
            ->count(),

        'partially_dispensed' => (clone $baseQuery)
            ->where('status', 'partial')
            ->count(),

        'completed_prescriptions' => (clone $baseQuery)
            ->where('status', 'completed')
            ->count(),

        'dispensed_today' => Dispensing::query()
            ->whereDate('dispensed_at', today())
            ->whereHas(
                'prescriptionItem.prescription.visit.doctor.facilityDepartmentSpecialization.facilityDepartment',
                fn (Builder $query) =>
                    $query->whereIn('facility_id', $facilityIds)
            )
            ->count(),
    ];
}

        if ($user->isLabStaff()) {
            return $this->labRequestSummary($user->accessibleFacilityIds());
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

    private function managementSummary(array $facilityIds, ?Carbon $from, ?Carbon $to): array
    {
        $facilityQuery = Facility::query();

        $facilityQuery->whereIn('id', $facilityIds);

        $appointmentQuery = Appointment::query()->whereHas(
            'doctor.facilityDepartmentSpecialization.facilityDepartment',
            fn(Builder $query) => $query->whereIn('facility_id', $facilityIds)
        );
        $visitQuery = Visit::query()->whereHas(
            'doctor.facilityDepartmentSpecialization.facilityDepartment',
            fn(Builder $query) => $query->whereIn('facility_id', $facilityIds)
        );

        return [
            'facilities' => $facilityQuery->count(),
            'doctors' => $this->doctorQuery($facilityIds)
                ->whereHas('employee', fn(Builder $query) => $query->where('is_active', true))
                ->count(),
            'pharmacists' => Pharmacist::query()
                ->whereHas('employee', fn(Builder $query) => $query
                    ->whereIn('facility_id', $facilityIds)
                    ->where('is_active', true))
                ->count(),
            'lab_staff' => LabStaff::query()
                ->whereHas('employee', fn(Builder $query) => $query
                    ->whereIn('facility_id', $facilityIds)
                    ->where('is_active', true))
                ->count(),
            'patients' => $this->patientQuery($facilityIds)->count(),
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

   private function labRequestSummary(array $facilityIds): array
{
    $query = LabRequestItem::query()
        ->whereHas(
            'visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment',
            fn (Builder $facilityQuery) =>
                $facilityQuery->whereIn('facility_id', $facilityIds)
        );

    $pending = (clone $query)
        ->where('status', 'pending')
        ->count();

    $processing = (clone $query)
        ->where('status', 'processing')
        ->count();

    $completed = (clone $query)
        ->where('status', 'completed')
        ->count();

    $completedToday = LabResult::query()
        ->whereDate('completed_at', today())
        ->whereHas(
            'labRequestItem.visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment',
            fn (Builder $facilityQuery) =>
                $facilityQuery->whereIn('facility_id', $facilityIds)
        )
        ->count();

    return [
        'pending_requests' => $pending,
        'in_progress_requests' => $processing,
        'completed_requests' => $completed,
        'completed_today' => $completedToday,
    ];
}

    private function doctorQuery(array $facilityIds): Builder
    {
        return Doctor::query()
            ->whereHas(
                'facilityDepartmentSpecialization.facilityDepartment',
                fn(Builder $facilityQuery) => $facilityQuery->whereIn('facility_id', $facilityIds)
            );
    }

    private function patientQuery(array $facilityIds): Builder
    {
        return Patient::query()->whereHas(
            'appointments.doctor.facilityDepartmentSpecialization.facilityDepartment',
            fn(Builder $facilityQuery) => $facilityQuery->whereIn('facility_id', $facilityIds)
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

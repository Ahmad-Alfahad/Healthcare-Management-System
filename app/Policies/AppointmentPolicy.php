<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isDoctor()
            || $user->isPatient();
    }

    /**
     * Admin can view anything.
     * Manager can view appointments of his facility.
     * Doctor can view his appointments.
     * Patient can view his appointments.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isManager()) {
            return $this->managerOwnsAppointment($user, $appointment);
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $appointment->doctor_id;
        }

        if ($user->isPatient()) {
            return $user->patient?->id === $appointment->patient_id;
        }

        return false;
    }

    /**
     * Admin can create appointments for any patient and doctor.
     * Patients can create appointments for themselves.
     * Doctors can create appointments for any patient using their own doctor account.
     */
    public function create(
        User $user,
        Patient $patient,
        Doctor $doctor
    ): bool {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPatient()) {
            return $user->patient?->id === $patient->id;
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $doctor->id;
        }

        return false;
    }

    /**
     * Admin can update anything.
     * Manager can update appointments in his facility.
     * Doctor can update his appointments.
     * Patient can update his own appointments.
     */
    public function update(
        User $user,
        Appointment $appointment
    ): bool {
        return $this->canManageAppointment($user, $appointment);
    }

    /**
     * Admin can delete anything.
     * Manager can delete appointments in his facility.
     * Doctor can delete his appointments.
     * Patient can delete his own appointments.
     */
    public function delete(
        User $user,
        Appointment $appointment
    ): bool {
        return $this->canManageAppointment($user, $appointment);
    }

    /**
     * Status changes are allowed for:
     * Admin
     * Manager of the doctor's facility
     * Doctor who owns the appointment
     *
     * Patients can only cancel their own appointment before its start time.
     */
    public function changeStatus(
        User $user,
        Appointment $appointment,
        string $newStatus
    ): bool {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $appointment->doctor_id;
        }

        if ($user->isManager()) {
            return $this->managerOwnsAppointment($user, $appointment);
        }

        if ($user->isPatient()) {
            return $newStatus === 'cancelled'
                && $user->patient?->id === $appointment->patient_id
                && Carbon::parse($appointment->scheduled_date . ' ' . $appointment->start_time)->isFuture();
        }

        return false;
    }

    private function canManageAppointment(
        User $user,
        Appointment $appointment
    ): bool {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPatient()) {
            return $user->patient?->id === $appointment->patient_id;
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $appointment->doctor_id;
        }

        if ($user->isManager()) {
            return $this->managerOwnsAppointment($user, $appointment);
        }

        return false;
    }

    private function managerOwnsAppointment(
        User $user,
        Appointment $appointment
    ): bool {
        $facility = $appointment->doctor
            ?->facilityDepartmentSpecialization
            ?->facilityDepartment
            ?->facility;

        return $facility !== null
            && $user->managesFacility($facility);
    }
}

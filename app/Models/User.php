<?php

namespace App\Models;

use App\Models\Doctor;
use App\Models\Profile;
use App\Models\Patient;
use App\Models\Facility;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, \App\Models\Concerns\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isPatient(): bool
    {
        return $this->hasRole('patient');
    }

    public function isDoctor(): bool
    {
        return $this->hasRole('doctor');
    }

    public function isPharmacist(): bool
    {
        return $this->hasRole('pharmacist');
    }

    public function isLabStaff(): bool
    {
        return $this->hasRole('laboratory');
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole([
            'doctor',
            'pharmacist',
            'laboratory',
        ]);
    }

    public function isManagement(): bool
    {
        return $this->hasAnyRole([
            'admin',
            'manager',
        ]);
    }

    public function doctor(): HasOneThrough
    {
        return $this->hasOneThrough(
            Doctor::class,
            Profile::class,
            'user_id',
            'profile_id',
            'id',
            'id'
        );
    }

    public function pharmacist(): HasOneThrough
    {
        return $this->hasOneThrough(
            Pharmacist::class,
            Profile::class,
            'user_id',
            'profile_id',
            'id',
            'id'
        );
    }

    public function labStaff(): HasOneThrough
    {
        return $this->hasOneThrough(
            LabStaff::class,
            Profile::class,
            'user_id',
            'profile_id',
            'id',
            'id'
        );
    }

    public function facility(): ?Facility
    {
        if ($this->doctor) {
            return $this->doctor
                ->facilityDepartmentSpecialization
                ?->facilityDepartment
                ?->facility;
        }

        if ($this->pharmacist) {
            return $this->pharmacist->facility;
        }

        if ($this->labStaff) {
            return $this->labStaff->facility;
        }

        return null;
    }

    public function accessibleFacilityIds(): array
    {
        $facility = $this->facility();

        return array_values(array_unique(array_filter([
            $facility?->id,
            $facility?->parent_id,
        ])));
    }

    public function managesFacility(Facility $facility): bool
    {
        return $this->hasRole('manager')
            && $this->facility()?->id === $facility->id;
    }

    public function patient(): HasOneThrough
    {
        return $this->hasOneThrough(
            Patient::class,
            Profile::class,
            'user_id',
            'profile_id',
            'id',
            'id'
        );
    }
}

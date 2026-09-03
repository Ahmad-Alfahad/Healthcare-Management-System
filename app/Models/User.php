<?php

namespace App\Models;

use App\Models\Doctor;
use App\Models\Profile;
use App\Models\Patient;
use App\Models\Facility;
use App\Models\Pharmacist;
use App\Models\LabStaff;
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

    public function employee(): HasOneThrough
    {
        return $this->hasOneThrough(
            Employee::class,
            Profile::class,
            'user_id',
            'profile_id',
            'id',
            'id'
        );
    }

    public function getDoctorAttribute(): ?Doctor
    {
        if ($this->relationLoaded('doctor')) {
            return $this->getRelation('doctor');
        }

        return $this->employee?->doctor;
    }

    public function getPharmacistAttribute(): ?Pharmacist
    {
        if ($this->relationLoaded('pharmacist')) {
            return $this->getRelation('pharmacist');
        }

        return $this->employee?->pharmacist;
    }

    public function getLabStaffAttribute(): ?LabStaff
    {
        if ($this->relationLoaded('labStaff')) {
            return $this->getRelation('labStaff');
        }

        return $this->employee?->labStaff;
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


    public function facility(): ?Facility
    {
        return $this->employee?->facility;
    }

    public function accessibleFacilityIds(): array
    {
        $facility = $this->facility();

        if (!$facility) {
            return [];
        }

        return $facility->familyIds();
    }

    public function managesFacility(Facility $facility): bool
    {
        return $this->hasRole('manager')
            && in_array($facility->id, $this->accessibleFacilityIds(), true);
    }
}

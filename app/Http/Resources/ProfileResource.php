<?php 
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $role = $user->roles->first()->name ?? 'guest';

        $data = [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'role' => $role,
        ];

        if ($role === 'doctor' && $user->relationLoaded('doctor')) {
            $data['doctor_info'] = [
                'specialization' => $user->doctor->specialization->name ?? null,
                'facility' => $user->doctor->facility->name ?? null,
            ];
        }

        if ($role === 'patient' && $user->relationLoaded('patient')) {
            $data['patient_info'] = [
                'blood_type' => $user->patient->blood_type ?? null,
            ];
        }

      
        return $data;
    }
}
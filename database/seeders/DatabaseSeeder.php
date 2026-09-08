<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Auditable records require an authenticated user. Seeds are fixture data,
        // so events are suppressed only for this command; runtime auditing is unchanged.
        Model::withoutEvents(function (): void {
            $this->call([
                ReferenceDataSeeder::class,
                HealthcareScenarioSeeder::class,
            ]);

            $this->assertSeedConsistency();
        });
    }

    private function assertSeedConsistency(): void
    {
        $invalidResults = DB::table('lab_results')
            ->join(
                'lab_request_items',
                'lab_request_items.id',
                '=',
                'lab_results.lab_request_item_id'
            )
            ->where('lab_request_items.status', '!=', 'completed')
            ->count();

        if ($invalidResults > 0) {
            throw new \RuntimeException(
                'Seed consistency failed: non-completed lab requests have results.'
            );
        }

        $completedWithoutResults = DB::table('lab_request_items')
            ->leftJoin(
                'lab_results',
                'lab_results.lab_request_item_id',
                '=',
                'lab_request_items.id'
            )
            ->where('lab_request_items.status', 'completed')
            ->whereNull('lab_results.id')
            ->count();

        if ($completedWithoutResults > 0) {
            throw new \RuntimeException(
                'Seed consistency failed: completed lab requests lack results.'
            );
        }

        $invalidVisits = DB::table('visits')
            ->join(
                'appointments',
                'appointments.id',
                '=',
                'visits.appointment_id'
            )
            ->whereColumn('visits.doctor_id', '!=', 'appointments.doctor_id')
            ->orWhereColumn('visits.patient_id', '!=', 'appointments.patient_id')
            ->count();

        if ($invalidVisits > 0) {
            throw new \RuntimeException(
                'Seed consistency failed: visit and appointment participants differ.'
            );
        }

        $prescriptions = DB::table('prescriptions')
            ->whereIn('status', ['pending', 'partial', 'dispensed'])
            ->get(['id', 'status']);

        foreach ($prescriptions as $prescription) {
            $items = DB::table('prescription_items')
                ->where('prescription_id', $prescription->id)
                ->get(['id', 'quantity_prescribed']);

            $totalPrescribed = 0;
            $totalDispensed = 0;

            foreach ($items as $item) {
                $totalPrescribed += (int) $item->quantity_prescribed;
                $totalDispensed += (int) DB::table('dispensings')
                    ->where('prescription_item_id', $item->id)
                    ->sum('quantity_dispensed');
            }

            $expectedStatus = $totalDispensed === 0
                ? 'pending'
                : ($totalDispensed >= $totalPrescribed
                    ? 'dispensed'
                    : 'partial');

            if ($prescription->status !== $expectedStatus) {
                throw new \RuntimeException(
                    "Seed consistency failed: prescription {$prescription->id} "
                    ."has status {$prescription->status}, expected {$expectedStatus}."
                );
            }
        }
    }
}

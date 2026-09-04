<?php

return [
    /*
     * These values are intentionally environment driven so a developer can
     * generate a larger realistic data set without changing seed code.
     */
    'password' => env('HEALTHCARE_SEED_PASSWORD', 'password123'),
    'facilities' => (int) env('HEALTHCARE_SEED_FACILITIES', 5),
    'doctors' => (int) env('HEALTHCARE_SEED_DOCTORS', 10),
    'pharmacists' => (int) env('HEALTHCARE_SEED_PHARMACISTS', 10),
    'lab_staff' => (int) env('HEALTHCARE_SEED_LAB_STAFF', 10),
    'patients' => (int) env('HEALTHCARE_SEED_PATIENTS', 100),
    'transaction_patients' => (int) env('HEALTHCARE_SEED_TRANSACTION_PATIENTS', 48),
];

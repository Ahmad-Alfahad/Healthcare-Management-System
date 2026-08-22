<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\DashboardRepository;

class DashboardService
{
    public function __construct(private DashboardRepository $dashboardRepository) {}

    public function getFor(User $user, string $period = 'current_month'): array
    {
        return $this->dashboardRepository->summaryFor($user, $period);
    }
}

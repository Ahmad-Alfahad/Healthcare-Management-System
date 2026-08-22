<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $period = $request->validate([
            'period' => ['sometimes', 'in:current_month,last_month,all'],
        ])['period'] ?? 'current_month';

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $this->dashboardRole($user),
                'period' => $period,
                'summary' => $this->dashboardService->getFor($user, $period),
            ],
        ]);
    }

    private function dashboardRole($user): string
    {
        foreach (['admin', 'manager', 'patient', 'doctor', 'pharmacist', 'laboratory'] as $role) {
            if ($user->hasRole($role)) {
                return $role;
            }
        }

        return 'user';
    }
}

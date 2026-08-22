<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'table_name' => ['sometimes', 'string', 'max:255'],
            'action' => ['sometimes', 'in:create,update,delete'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'record_id' => ['sometimes', 'integer'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date', 'after_or_equal:from'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $auditLogs = AuditLog::query()
            ->with('user:id,name,email')
            ->when($request->user()->isManager(), function ($query) use ($request): void {
                $query->where('facility_id', $request->user()->facility()?->id);
            })
            ->when($filters['table_name'] ?? null, fn($query, $value) => $query->where('table_name', $value))
            ->when($filters['action'] ?? null, fn($query, $value) => $query->where('action', $value))
            ->when($filters['user_id'] ?? null, fn($query, $value) => $query->where('user_id', $value))
            ->when($filters['record_id'] ?? null, fn($query, $value) => $query->where('record_id', $value))
            ->when($filters['from'] ?? null, fn($query, $value) => $query->whereDate('created_at', '>=', $value))
            ->when($filters['to'] ?? null, fn($query, $value) => $query->whereDate('created_at', '<=', $value))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 25)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $auditLogs,
        ], Response::HTTP_OK);
    }

    public function show(Request $request, AuditLog $auditLog): JsonResponse
    {
        abort_unless(
            $request->user()->isAdmin()
                || ($request->user()->isManager()
                    && $auditLog->facility_id === $request->user()->facility()?->id),
            403,
            'You do not have access to this audit log.'
        );

        return response()->json([
            'success' => true,
            'data' => $auditLog->load('user:id,name,email'),
        ], Response::HTTP_OK);
    }
}

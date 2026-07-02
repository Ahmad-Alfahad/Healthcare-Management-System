<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $requiredRoles = collect($roles)
            ->flatMap(fn(string $role): array => preg_split('/[\s,|]+/', $role) ?: [])
            ->map(fn(string $role): string => trim($role))
            ->filter()
            ->values()
            ->all();

        if (empty($requiredRoles)) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        if (! method_exists($user, 'hasAnyRole')) {
            abort(500, 'The authenticated user model does not support role checks.');
        }

        if ($user->hasAnyRole($requiredRoles)) {
            return $next($request);
        }

        abort(403, 'You do not have the required role.');
    }
}

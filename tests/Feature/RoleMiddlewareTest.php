<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_required_role_is_blocked(): void
    {
        $user = User::factory()->create();

        Route::middleware(['auth:sanctum', 'role:admin'])->get('/test-role-blocked', function () {
            return response()->json(['message' => 'ok']);
        });

        $response = $this->actingAs($user, 'sanctum')->getJson('/test-role-blocked');

        $response->assertForbidden();
    }

    public function test_user_with_required_role_can_access_when_roles_are_comma_separated(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin']);
        $user->assignRole('admin');

        Route::middleware(['auth:sanctum', 'role:admin,manager'])->get('/test-role-allowed', function () {
            return response()->json(['message' => 'ok']);
        });

        $response = $this->actingAs($user, 'sanctum')->getJson('/test-role-allowed');

        $response->assertOk()
            ->assertJsonPath('message', 'ok');
    }
}

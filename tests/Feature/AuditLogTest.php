<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_model_changes_are_audited_without_secrets(): void
    {
        $actor = User::factory()->create();
        $this->actingAs($actor, 'sanctum');

        $target = User::factory()->create([
            'name' => 'Original name',
            'email' => 'original@example.com',
        ]);

        $createdLog = AuditLog::query()
            ->where('table_name', 'users')
            ->where('action', 'create')
            ->where('record_id', $target->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('Original name', $createdLog->new_value['name']);
        $this->assertArrayNotHasKey('password', $createdLog->new_value);

        $target->update([
            'name' => 'Updated name',
            'email' => 'updated@example.com',
        ]);

        $updatedLog = AuditLog::query()
            ->where('table_name', 'users')
            ->where('action', 'update')
            ->where('record_id', $target->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('Original name', $updatedLog->old_value['name']);
        $this->assertSame('Updated name', $updatedLog->new_value['name']);

        $target->delete();

        $deletedLog = AuditLog::query()
            ->where('table_name', 'users')
            ->where('action', 'delete')
            ->where('record_id', $target->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('Updated name', $deletedLog->old_value['name']);
        $this->assertNull($deletedLog->new_value);
    }
}

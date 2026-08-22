<?php

namespace App\Models\Concerns;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

trait Auditable
{
    public static function bootAuditable(): void
    {
        Event::listen('eloquent.created: ' . static::class, function (Model $model): void {
            app(AuditLogger::class)->record(
                'create',
                $model,
                null,
                $model->getAttributes()
            );
        });

        Event::listen('eloquent.updated: ' . static::class, function (Model $model): void {
            $changes = $model->getChanges();

            if ($changes === []) {
                return;
            }

            $original = [];
            foreach (array_keys($changes) as $key) {
                $original[$key] = $model->getOriginal($key);
            }

            app(AuditLogger::class)->record(
                'update',
                $model,
                $original,
                $changes
            );
        });

        Event::listen('eloquent.deleted: ' . static::class, function (Model $model): void {
            app(AuditLogger::class)->record(
                'delete',
                $model,
                $model->getAttributes(),
                null
            );
        });
    }
}

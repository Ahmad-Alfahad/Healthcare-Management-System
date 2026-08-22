<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait ListQuery
{
    protected function paginateList(
        Builder $query,
        array $filters,
        array $columns = [],
        array $relations = [],
        array $exactFilters = [],
        array $dateFilters = []
    ): LengthAwarePaginator {
        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search, $columns, $relations): void {
                foreach ($columns as $column) {
                    $builder->orWhere($column, 'like', "%{$search}%");
                }

                foreach ($relations as $relation => $relationColumns) {
                    $builder->orWhereHas($relation, function (Builder $related) use ($search, $relationColumns): void {
                        $related->where(function (Builder $nested) use ($search, $relationColumns): void {
                            foreach ($relationColumns as $column) {
                                $nested->orWhere($column, 'like', "%{$search}%");
                            }
                        });
                    });
                }
            });
        }

        foreach ($exactFilters as $filter => $column) {
            if (($filters[$filter] ?? null) !== null && $filters[$filter] !== '') {
                $query->where($column, $filters[$filter]);
            }
        }

        foreach ($dateFilters as $filter => [$column, $operator]) {
            if (($filters[$filter] ?? null) !== null && $filters[$filter] !== '') {
                $query->whereDate($column, $operator, $filters[$filter]);
            }
        }

        return $query
            ->latest('id')
            ->paginate((int) ($filters['per_page'] ?? 10), ['*'], 'page', (int) ($filters['page'] ?? 1))
            ->withQueryString();
    }
}

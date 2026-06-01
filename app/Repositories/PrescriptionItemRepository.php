<?php

namespace App\Repositories;

use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Collection;

class PrescriptionItemRepository
{
    public function all(): Collection
    {
        return PrescriptionItem::with('prescription')->get();
    }

    public function find(int $id): PrescriptionItem
    {
        return PrescriptionItem::with('prescription')->findOrFail($id);
    }

    public function create(array $data): PrescriptionItem
    {
        return PrescriptionItem::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $item = PrescriptionItem::findOrFail($id);
        return $item->update($data);
    }

    public function delete(int $id): bool
    {
        $item = PrescriptionItem::findOrFail($id);
        return $item->delete();
    }
}
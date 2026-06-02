<?php
namespace App\Repositories;

use App\Models\LabRequestItem;
use Illuminate\Database\Eloquent\Collection;

class LabRequestItemRepository
{
    public function all(): Collection
    {
        return LabRequestItem::with(['visit', 'labTest'])->get();
    }

    public function find(int $id): LabRequestItem
    {
        return LabRequestItem::with(['visit', 'labTest'])->find($id);
    }

    public function create(array $data): LabRequestItem
    {
        return LabRequestItem::create($data);
    }

    public function update(LabRequestItem $labRequestItem, array $data): bool
    {
        return $labRequestItem->update($data);
    }

    public function delete(LabRequestItem $labRequestItem): bool
    {
        return $labRequestItem->delete();
    }
}
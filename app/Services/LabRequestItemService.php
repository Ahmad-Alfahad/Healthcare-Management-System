<?php 
namespace App\Services;

use App\Models\LabRequestItem;
use App\Repositories\LabRequestItemRepository;
use Illuminate\Database\Eloquent\Collection;

class LabRequestItemService
{
    protected $labRequestItemRepository;

    public function __construct(LabRequestItemRepository $labRequestItemRepository)
    {
        $this->labRequestItemRepository = $labRequestItemRepository;
    }

    public function getAllLabRequestItems(): Collection
    {
        return $this->labRequestItemRepository->all();
    }

    public function getLabRequestItemById(int $id): LabRequestItem
    {
        return $this->labRequestItemRepository->find($id);
    }

    public function createLabRequestItem(array $data): LabRequestItem
    {
        return $this->labRequestItemRepository->create($data);
    }

    public function updateLabRequestItem(LabRequestItem $labRequestItem, array $data): bool
    {
        return $this->labRequestItemRepository->update($labRequestItem, $data);
    }

    public function deleteLabRequestItem(LabRequestItem $labRequestItem): bool
    {
        return $this->labRequestItemRepository->delete($labRequestItem);
    }
}
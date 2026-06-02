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

    public function all(): Collection
    {
        return $this->labRequestItemRepository->all();
    }

    public function find(int $id): LabRequestItem
    {
        return $this->labRequestItemRepository->find($id);
    }

    public function create(array $data): LabRequestItem
    {
        return $this->labRequestItemRepository->create($data);
    }

    public function update(LabRequestItem $labRequestItem, array $data): bool
    {
        return $this->labRequestItemRepository->update($labRequestItem, $data);
    }

    public function delete(LabRequestItem $labRequestItem): bool
    {
        return $this->labRequestItemRepository->delete($labRequestItem);
    }
}
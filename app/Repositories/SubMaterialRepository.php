<?php

namespace App\Repositories;

use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Models\SubMaterial;

class SubMaterialRepository implements SubMaterialRepositoryInterface
{
    public function all()
    {
        return SubMaterial::all();
    }

    public function find($id)
    {
        return SubMaterial::find($id);
    }

    public function create(array $data)
    {
        return SubMaterial::create($data);
    }

    public function update($id, array $data)
    {
        $subMaterial = $this->find($id);
        if ($subMaterial) {
            $subMaterial->update($data);
            return $subMaterial;
        }
        return null;
    }

    public function delete($id)
    {
        $subMaterial = $this->find($id);
        if ($subMaterial) {
            return $subMaterial->delete();
        }
        return false;
    }

    public function getAllByMaterial($materialId)
    {
        return SubMaterial::where('material_id', $materialId)
            ->ordered()
            ->get();
    }

    public function findByMaterial($materialId)
    {
        return $this->getAllByMaterial($materialId);
    }

    public function reorder($materialId, array $orderData)
    {
        foreach ($orderData as $order => $subMaterialId) {
            SubMaterial::where('id', $subMaterialId)
                ->where('material_id', $materialId)
                ->update(['order' => $order + 1]);
        }
        
        return true;
    }

    public function findWithQuestions($id)
    {
        return SubMaterial::with('questions')->findOrFail($id);
    }
}

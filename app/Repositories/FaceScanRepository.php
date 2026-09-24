<?php

namespace App\Repositories;

use App\Models\FaceScan;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use Illuminate\Support\Collection;

class FaceScanRepository implements FaceScanRepositoryInterface
{
    public function create(array $attributes): FaceScan
    {
        return FaceScan::create($attributes);
    }

    public function find(int $id): ?FaceScan
    {
        return FaceScan::with('card')->find($id);
    }

    public function findOrFail(int $id): FaceScan
    {
        return FaceScan::with('card')->findOrFail($id);
    }

    public function forUser(int $userId): Collection
    {
        return FaceScan::where('user_id', $userId)->latest()->get();
    }

    public function update(FaceScan $scan, array $attributes): FaceScan
    {
        $scan->update($attributes);

        return $scan->fresh('card');
    }
}

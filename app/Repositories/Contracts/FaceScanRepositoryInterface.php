<?php

namespace App\Repositories\Contracts;

use App\Models\FaceScan;
use Illuminate\Support\Collection;

interface FaceScanRepositoryInterface
{
    public function create(array $attributes): FaceScan;

    public function find(int $id): ?FaceScan;

    public function findOrFail(int $id): FaceScan;

    public function forUser(int $userId): Collection;

    public function update(FaceScan $scan, array $attributes): FaceScan;
}

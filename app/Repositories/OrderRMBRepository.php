<?php

namespace App\Repositories;

use App\Models\OrderRMB;

class OrderRMBRepository implements OrderRepositoryInterface
{
    public function store(array $attributes)
    {
        return OrderRMB::query()->create($attributes);
    }

    public function show(string $id)
    {
        return OrderRMB::query()->find($id);
    }
}

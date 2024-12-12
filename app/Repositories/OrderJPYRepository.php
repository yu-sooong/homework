<?php

namespace App\Repositories;

use App\Models\OrderJPY;

class OrderJPYRepository implements OrderRepositoryInterface
{
    public function store(array $attributes)
    {
        return OrderJPY::query()->create($attributes);
    }

    public function show(string $id)
    {
        return OrderJPY::query()->find($id);
    }
}

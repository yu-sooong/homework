<?php

namespace App\Repositories;

use App\Models\OrderMYR;

class OrderMYRRepository implements OrderRepositoryInterface
{
    public function store(array $attributes)
    {
        return OrderMYR::query()->create($attributes);
    }

    public function show(string $id)
    {
        return OrderMYR::query()->find($id);
    }
}

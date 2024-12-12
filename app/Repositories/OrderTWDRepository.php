<?php

namespace App\Repositories;

use App\Enums\Currency;
use App\Models\OrderTWD;
use Illuminate\Support\Facades\DB;

class OrderTWDRepository implements OrderRepositoryInterface
{
    public function store(array $attributes)
    {
        return OrderTWD::query()->create($attributes);
    }

    public function show(string $id)
    {
        return OrderTWD::query()->find($id);
    }
}

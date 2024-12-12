<?php

namespace App\Repositories;

use App\Enums\Currency;
use App\Models\OrderUSD;
use Illuminate\Support\Facades\DB;

class OrderUSDRepository implements OrderRepositoryInterface
{
    public function store(array $attributes)
    {
        return OrderUSD::query()->create($attributes);
    }

    public function show(string $id)
    {
        return OrderUSD::query()->find($id);
    }
}

<?php

namespace App\Repositories;

interface OrderRepositoryInterface
{
    public function store(array $attributes);

    public function show(string $id);
}

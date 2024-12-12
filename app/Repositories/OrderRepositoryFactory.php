<?php

namespace App\Repositories;

use App\Enums\Currency;
use Illuminate\Support\Facades\DB;

class OrderRepositoryFactory
{
    /**
     * 訂單工廠、負責產生相對應的 repository
     *
     * @param Currency $currency
     * @return OrderRepositoryInterface
     * @throws \Exception
     */
    public function make(Currency $currency): OrderRepositoryInterface
    {
        // 使用 Enums 收斂、產出對應的 Repository
        return match ($currency) {
            Currency::TWD => new OrderTWDRepository(),
            Currency::USD => new OrderUsdRepository(),
            Currency::JPY => new OrderJpyRepository(),
            Currency::RMB => new OrderRmbRepository(),
            Currency::MYR => new OrderMyrRepository(),
            default => throw new \Exception("Unsupported currency: $currency->value")
        };
    }
}

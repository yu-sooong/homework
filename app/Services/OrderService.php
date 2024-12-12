<?php

namespace App\Services;

use App\Enums\Currency;
use App\Repositories\OrderRepositoryFactory;

class OrderService
{
    public function __construct(protected OrderRepositoryFactory $repositoryFactory)
    {
    }

    /**
     * 處理訂單並建立資料到對應的資料表
     *
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    public function createOrder(array $attributes): mixed
    {
        try {
            // 取得 currency (幣別) 轉換為 Enum 類型
            $currency = Currency::from(data_get($attributes, 'currency'));

            // 使用工廠模式創建對應的 Repository
            $repository = $this->repositoryFactory->make($currency);

            // 使用對應的 Repository 儲存訂單資料
            return $repository->store($attributes);
        } catch (\Throwable $e) {
            throw new \Exception('something went wrong', 400, $e);
        }
    }


    /**
     * 取得單筆訂單資料
     *
     * @param string $id
     * @param string $currency
     * @return mixed
     * @throws \Exception
     */
    public function findOrder(string $id, string $currency): mixed
    {
        try {
            // 取得 currency (幣別) 轉換為 Enum 類型
            $currency = Currency::from($currency);

            // 使用工廠模式創建對應的 Repository
            $repository = $this->repositoryFactory->make($currency);

            // 使用對應的 Repository 取得單筆訂單資料
            return $repository->show($id) ?? [];
        } catch (\Throwable $e) {
            throw new \Exception('something went wrong', 400, $e);
        }
    }
}

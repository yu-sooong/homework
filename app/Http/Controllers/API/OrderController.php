<?php

namespace App\Http\Controllers\API;

use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderShowRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function store(OrderCreateRequest $request): JsonResponse
    {
        try {
            event(
                new OrderCreated(
                    $request->only([
                        'id',
                        'name',
                        'address',
                        'price',
                        'currency'
                    ])
                )
            );
        } catch (Throwable $e) {
            Log::error('something went wrong', ['message' => $e->getMessage()]);
        }

        // 回給前端 200 代表已接收請求、並開始執行作業 (非同步)
        return response()->json([
            'message' => 'Request accepted and is being processed.',
        ]);
    }

    /**
     * 取得單筆訂單資料
     *
     * @param OrderShowRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function show(OrderShowRequest $request, $id): JsonResponse
    {
        try {
            return response()->json($this->orderService->findOrder($id, $request->input('currency')));
        } catch (Throwable $e) {
            Log::error('something went wrong', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'something went wrong'], 400);
        }
    }
}

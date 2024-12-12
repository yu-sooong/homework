<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderCreateProcessed implements ShouldQueue
{
    // 重試次數
    public int $tries = 3;
    public int $timeout = 60 * 2;

    /**
     * Create the event listener.
     */
    public function __construct(protected OrderService $orderService)
    {
        //
    }

    /**
     * Handle the event.
     * @throws Throwable
     */
    public function handle(OrderCreated $event): void
    {
        try {
            $this->orderService->createOrder($event->order);
        } catch (Throwable $e) {
            Log::error('Failed to process order', [
                'message'    => $e->getMessage(),
                'order_data' => $event->order,
            ]);
            throw $e;
        }
    }
}

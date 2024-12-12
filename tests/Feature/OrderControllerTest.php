<?php

use App\Enums\Currency;
use Database\Factories\OrderTWDFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Events\OrderCreated;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreOrder()
    {
        $attributes = [
            'id'       => 'A0000001',
            'name'     => 'Test Order',
            'address'  => [
                "city"     => "taipei-city",
                "district" => "da-an-district",
                "street"   => "fuxing-south-road"
            ],
            'price'    => 100,
            'currency' => Currency::TWD->value,
        ];

        // 假設事件被觸發
        Event::fake();

        $response = $this->postJson('/api/orders', $attributes);

        // 確認返回 200
        $response->assertStatus(200);

        // 確認 OrderCreated 事件是否被觸發
        Event::assertDispatched(OrderCreated::class);
    }

    public function testStoreWithInvalidCurrencyHandledByController()
    {
        $attributes = [
            'id'       => 'A0000001',
            'name'     => 'Test Order',
            'address'  => [
                "city"     => "taipei-city",
                "district" => "da-an-district",
                "street"   => "fuxing-south-road"
            ],
            'price'    => 100,
            'currency' => 'INVALID_CURRENCY', // 給錯誤的值
        ];


        $response = $this->postJson('/api/orders', $attributes);

        // 斷言 422 錯誤
        $response->assertStatus(422);

        // 斷言錯誤的結構、結構定義在 app.php
        /*
         * $exceptions->render(function (Throwable $e) {
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors'  => $e->errors()
                ], $e->status);
            }
        });
         * */
        $response->assertJson([
            "message" => "The selected currency is invalid.",
            "errors"  => [
                "currency" => [
                    "The selected currency is invalid."
                ]
            ]
        ]);
    }

    public function testShowOrderSuccess()
    {
        // 模擬輸入的請求參數
        $id = 'A0000001';
        $currency = Currency::TWD->value;

        // 使用工廠創建訂單資料
        OrderTWDFactory::new()->create([
            'id'       => $id,
            'currency' => $currency,
            'name'     => 'Test Order',
            'price'    => 100,
            'address'  => json_encode([
                'city'     => 'taipei-city',
                'district' => 'da-an-district',
                'street'   => 'fuxing-south-road'
            ]),
        ]);

        // 發送 GET 請求到 'show' 方法
        $response = $this->get("/api/orders/$id?currency=$currency");

        // 斷言回應為 200 且回應資料與預期相符
        $response->assertStatus(200)
            ->assertJson([
                'id'       => $id,
                'currency' => $currency,
                'name'     => 'Test Order',
                'price'    => 100,
                'address'  => json_encode([
                    'city'     => 'taipei-city',
                    'district' => 'da-an-district',
                    'street'   => 'fuxing-south-road'
                ]),
            ]);
    }

    public function testShowOrderFailureInvalidCurrency()
    {
        // 模擬輸入的請求參數
        $id = 'A0000001';
        $invalidCurrency = 'INVALID_CURRENCY'; // 假設這個是無效的幣別

        // 發送 GET 請求到 'show' 方法，使用無效的幣別
        $response = $this->get("/api/orders/$id?currency=$invalidCurrency");

        // 斷言回應為 422，並且回應資料與預期相符
        $response->assertStatus(422)
            ->assertJson([
                "message" => "The selected currency is invalid.",
                "errors"  => [
                    "currency" => [
                        "The selected currency is invalid."
                    ]
                ]
            ]);
    }

}

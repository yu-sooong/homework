<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\OrderService;
use App\Repositories\OrderRepositoryFactory;
use App\Enums\Currency;
use App\Repositories\OrderRepositoryInterface;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $repositoryFactory;
    protected $orderService;

    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryFactory = Mockery::mock(OrderRepositoryFactory::class);
        $this->orderService = new OrderService($this->repositoryFactory);
    }

    public function testCreateOrderWithValidCurrency()
    {
        // 測試 OrderService::createOrder 方法
        // 測試目標: 確認 createOrder 方法是否能正確地處理訂單資料並交給對應的 Repository 儲存。
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

        $mockRepository = Mockery::mock(OrderRepositoryInterface::class);

        // 測試透過接收幣別 讓工廠 make 出 repository
        $this->repositoryFactory
            ->shouldReceive('make')
            ->with(Currency::TWD)
            ->andReturn($mockRepository);

        // repository 測建立訂單結果
        $mockRepository
            ->shouldReceive('store')
            ->with($attributes)
            ->andReturn(true);

        $result = $this->orderService->createOrder($attributes);

        $this->assertTrue($result);
    }

    public function testCreateOrderWithInvalidCurrency()
    {
        // 測試 確認 createOrder 處理無效的幣別的時候會拋出異常
        $mockRepositoryFactory = $this->createMock(OrderRepositoryFactory::class);

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

        $orderService = new OrderService($mockRepositoryFactory);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('something went wrong');

        $orderService->createOrder($attributes);
    }

    public function testFindOrderSuccess()
    {
        $id = 'A0000001';
        $currency = Currency::TWD->value;
        $attributes = [
            'id'       => $id,
            'name'     => 'Test Order',
            'address'  => [
                "city"     => "taipei-city",
                "district" => "da-an-district",
                "street"   => "fuxing-south-road"
            ],
            'price'    => 100,
            'currency' => $currency,
        ];

        // 模擬 Repository
        $mockRepository = Mockery::mock(OrderRepositoryInterface::class);

        // 測試透過接收幣別 讓工廠 make 出 repository
        $this->repositoryFactory
            ->shouldReceive('make')
            ->with(Currency::TWD)
            ->andReturn($mockRepository);

        // 模擬 store 呼叫
        $mockRepository
            ->shouldReceive('store')
            ->with($attributes)
            ->andReturn(true);

        // 執行 createOrder 方法
        $this->orderService->createOrder($attributes);

        // 驗證 store 是否被呼叫過
        $mockRepository->shouldHaveReceived('store')->once()->with($attributes);

        // 模擬 show 呼叫
        $mockRepository
            ->shouldReceive('show')
            ->with($id)
            ->andReturn($attributes);

        // 測試 findOrder 方法
        $result = $this->orderService->findOrder($id, Currency::TWD->value);

        // 驗證結果
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('currency', $result);
        $this->assertEquals($id, $result['id']);
        $this->assertEquals($currency, $result['currency']);
    }

    public function testFindOrderNotFound()
    {
        // 設置測試資料
        $id = 'A0000001';

        // 模擬 Repository
        $mockRepository = Mockery::mock(OrderRepositoryInterface::class);

        // 測試透過接收幣別 讓工廠 make 出 repository
        $this->repositoryFactory
            ->shouldReceive('make')
            ->with(Currency::TWD)
            ->andReturn($mockRepository);

        // 模擬 show 方法，當找不到訂單時返回 null
        $mockRepository
            ->shouldReceive('show')
            ->with($id)
            ->andReturn([]);  // 或返回空陣列: ->andReturn([])

        // 測試 findOrder 方法
        $result = $this->orderService->findOrder($id, Currency::TWD->value);

        // 驗證結果
        $this->assertEmpty($result);
        $this->assertEquals([], $result);
    }


    public function testFindOrderWithInvalidCurrency()
    {
        // 測試 確認 createOrder 處理無效的幣別的時候會拋出異常
        $mockRepositoryFactory = $this->createMock(OrderRepositoryFactory::class);

        $id = 'A0000001';
        $currency = 'INVALID_CURRENCY'; // 給錯誤的值

        $orderService = new OrderService($mockRepositoryFactory);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('something went wrong');

        $orderService->findOrder($id, $currency);
    }

    public function tearDown(): void
    {
        Mockery::close(); // Always close Mockery after the test
        parent::tearDown();
    }
}

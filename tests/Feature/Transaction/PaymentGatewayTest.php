<?php

use App\Services\LogService;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

test('successful deposit request', function () {
    $logService = app(LogService::class);
    $paymentGatewayService = new PaymentGatewayService('https://yourdomain.com', 'API_KEY', $logService);
 
    // Mock the HTTP response
    Http::fake([
        'yourdomain.com/deposit' => Http::response([
            'order_id' => '12345',
            'amount' => '5000.00',
            'status' => 1, // Success
        ], 200)
    ]);

    $requestData = [
        'order_id' => '12345',
        'amount' => 5000.00,
        'timestamp' => now()->toDateTimeString(),
    ];

    $response = $paymentGatewayService->deposit($requestData);

    $this->assertEquals('12345', $response['order_id']);
    $this->assertEquals(5000.00, $response['amount']);
    $this->assertEquals(1, $response['status']);
});

test('failed deposit request', function () {
    $logService = app(LogService::class);
    $paymentGatewayService = new PaymentGatewayService('https://yourdomain.com', 'API_KEY', $logService);
 
    // Mock the HTTP response for failure
    Http::fake([
        'yourdomain.com/deposit' => Http::response([
            'order_id' => '12345',
            'amount' => '5000.00',
            'status' => 2, // Failed
        ], 200)
    ]);

    $requestData = [
        'order_id' => '12345',
        'amount' => 5000.00,
        'timestamp' => now()->toDateTimeString(),
    ];

    try {
        $response = $paymentGatewayService->deposit($requestData);

        // We should not reach this assertion as an exception should be thrown
        $this->fail('Expected exception was not thrown');
    } catch (\Exception $e) {
        $this->assertStringContainsString('Payment gateway failed', $e->getMessage());
    }
});
<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PaymentGatewayService
{
    protected $baseUrl;
    protected $apiKey;
    protected $logService;

    public function __construct(string $baseUrl, string $apiKey, LogService $logService)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->logService = $logService;
    }

    public function deposit(array $req)
    {
        $this->logService->additionalRequest(['request'=> $req])->task('call_deposit_api_from_payment_gateway')->start();

        $depositUrl = $this->baseUrl.'/deposit';

        $rules = [
            'order_id' => ['required'],
            'amount' => ['required', 'numeric'],
            'timestamp' => ['sometimes', 'nullable', 'date'],
        ];

        $validator = Validator::make($req, $rules);

        if($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        try {
            // Make the HTTP POST request to the payment gateway
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->post($depositUrl, [
                'order_id' => $validated['order_id'],
                'amount' => round($validated['amount'], 2),
                'timestamp' => $validated['timestamp'] ?? now()->toDateTimeString(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                // Check if 'status' is present and if it's '1' (success)
                if (isset($responseData['status']) && $responseData['status'] == 1) {
                    return $responseData;
                } else {
                    throw new \Exception("Payment gateway failed: " . $response->body());
                }
            } else {
                throw new \Exception("Payment gateway error: " . $response->body());
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
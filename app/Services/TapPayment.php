<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class TapPayment
{
    private $mainTestUrl = 'https://api.tap.company/v2';
    private $mainProductionUrl = 'https://api.tap.company/v2';

    private $urls = [
        'makePayment' => '/charges',
        'retrievePayment' => '/charges/:id',
        // 'refundPayment' => '/payments/refund',
        // 'cancelPayment' => '/api/v1/payments/subscription/cancel',
    ];

    public function makePayment($data)
    {
        $secretKey = config('tap_payment.secretKey');
        $url = $this->mainProductionUrl . $this->urls['makePayment'];
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $secretKey",
                'content-type' => 'application/json'
            ])->post($url, $data);
            $body = json_decode($response->body());
            if (isset($body->errors)) {
                return [
                    'success' => false,
                    'response' => $body,
                ];
            }
            return [
                'success' => true,
                'response' => $body,
            ];
        } catch (ConnectionException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function retrievePayment($id)
    {
        $secretKey = config('tap_payment.secretKey');
        $url = $this->mainProductionUrl . (str_replace(':id', $id, $this->urls['retrievePayment']));
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $secretKey",
                'content-type' => 'application/json'
            ])->get($url);
            $body = json_decode($response->body());
            if (isset($body->errors)) {
                return [
                    'success' => false,
                    'response' => $body,
                ];
            }
            return [
                'success' => true,
                'response' => $body,
            ];
        } catch (ConnectionException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

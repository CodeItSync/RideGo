<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SkipCash
{
    private $mainTestUrl = 'https://skipcashtest.azurewebsites.net/api/v1';
    private $mainProductionUrl = 'https://api.skipcash.app/api/v1';

    private $urls = [
        'makePayment' => '/payments',
        'paymentDetails' => '/payments/:id',
        'refundPayment' => '/payments/refund',
        'cancelPayment' => '/api/v1/payments/subscription/cancel',
    ];

    public function makePayment($data)
    {
        $Uid = $this->guidv4();
        $KeyId = config('skipcash.keyId');
        $data = ['Uid' => $Uid, 'KeyId' => $KeyId] + $data;
        $url = $this->mainProductionUrl . $this->urls['makePayment'];
        $resultHeader = $this->getResultHeader($data);
        $authorizationToken = $this->getAuthorizationToken($resultHeader);
        try {
            $response = Http::withHeaders([
                'Authorization' => $authorizationToken,
            ])->post($url, $data);
        } catch (ConnectionException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        return [
            'success' => true,
            'response' => $response->body(),
        ];
    }

    private function guidv4($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function getResultHeader($data): string
    {
        $resultHeader = '';
        $i = 0;
        foreach ($data as $key => $value) {
            if ($i < count($data) and $i != 0) {
                $resultHeader .= ",";
            }
            $resultHeader .= "$key=$value";
            $i++;
        }
        return $resultHeader;
    }

    private function getAuthorizationToken($resultHeader): string
    {
        $secretKey = config('skipcash.secretKey');
        $s = hash_hmac('sha256', $resultHeader, $secretKey, true);
        return base64_encode($s);
    }

    public function paymentDetails($id): array
    {
        $authorizationToken = config('skipcash.clientKey');
        $url = $this->mainProductionUrl . $this->urls['paymentDetails'];
        $url = str_replace(':id', $id, $url);
        try {
            $response = Http::withHeaders([
                'Authorization' => $authorizationToken,
            ])->get($url);
        } catch (ConnectionException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        return [
            'success' => true,
            'response' => $response->body(),
        ];
    }

    public function refundPayment($transaction): array
    {
        $url = $this->mainProductionUrl . $this->urls['refundPayment'];
        $keyId = config('skipcash.keyId');
        $data = [
            "Id" => $transaction->transaction_id,
            "KeyId" => $keyId,
        ];
        $resultHeader = "Id=" . $data['Id'] . ',KeyId=' . $data['KeyId'];
        $authorizationToken = $this->getAuthorizationToken($resultHeader);
        try {
            $response = Http::withHeaders([
                'Authorization' => $authorizationToken,
            ])->post($url, $data);
        } catch (ConnectionException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        TransactionRefund::create([
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
        ]);
        return [
            'success' => true,
            'response' => $response->body(),
        ];
    }

    public function cancelPayment($id): array
    {
        $url = $this->mainProductionUrl . $this->urls['cancelPayment'];
        $keyId = config('skipcash.keyId');
        $data = [
            "Id" => $id,
            "KeyId" => $keyId,
        ];
        $resultHeader = "Id=" . $data['Id'] . ',KeyId=' . $data['KeyId'];
        $authorizationToken = $this->getAuthorizationToken($resultHeader);
        try {
            $response = Http::withHeaders([
                'Authorization' => $authorizationToken,
            ])->post($url, $data);
        } catch (ConnectionException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        return [
            'success' => true,
            'response' => $response->body(),
        ];
    }
}

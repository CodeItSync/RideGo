<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class FCMService
{
    private $projectId;

    public function __construct()
    {
        $this->projectId = config('mobile-config.FIREBASE.PROJECT_ID');
    }
    function insurePhoneNumberIsVerified(string $otpCode, string $phoneNumber):bool
    {
        $user = User::where('contact_number', $phoneNumber)->first();
        if (!$user || $otpCode != $user->otp_code) {
            return false;
        }
        return true;
    }

    function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}

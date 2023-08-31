<?php 

namespace App\Helper;

use Illuminate\Support\Facades\Http;

class Helper{
    public static function sendOtp($phoneNumber, $otp) {
        $response = Http::post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => config('app.semaphore_api_key'),
            'number' => $phoneNumber,
            'message' => "Your OTP is: $otp",
        ]);
    
        return $response->json();
    }

    public static function shortenDescription($description, $max_length) {
        if (strlen($description) > $max_length) {
            $shortened = substr($description, 0, $max_length - 3) . '...';
            return $shortened;
        } else {
            return $description;
        }
    }
}
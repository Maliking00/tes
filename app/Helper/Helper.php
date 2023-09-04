<?php

namespace App\Helper;

use Illuminate\Support\Facades\Http;

class Helper
{
    public static function sendOtp($phoneNumber, $otp)
    {
        session(['smsGatewayData' => array(
            'otp' => $otp,
            'phoneNumber' => $phoneNumber
        )]);
        Http::post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => config('app.semaphore_api_key'),
            'number' => $phoneNumber,
            'message' => "Your OTP is: $otp",
        ]);
        $response = array(
            'status' => true
        );
        return $response;
    }

    public static function shortenDescription($description, $max_length)
    {
        if (strlen($description) > $max_length) {
            $shortened = substr($description, 0, $max_length - 3) . '...';
            return $shortened;
        } else {
            return $description;
        }
    }

    public static function isExpired($sessionName)
    {
        $sessionTimeLimit = session($sessionName);

        if ($sessionTimeLimit) {
            $currentTime = now();

            if ($currentTime >= $sessionTimeLimit) {
                return true;
            }else{
                return false;
            }
        }
    }

    public static function otpPrivateNumberFormat($phone_number)
    {
        return preg_replace('/\d{8}(\d{3})/', '******$1', $phone_number);
    }

    public static function isAccessingPrivateUrl($session){
        if(!$session){
            abort(404, 'Resource Not Found');
        }
    }

    public static function isAcaddemicFocusRoutes($routeName){
        $arrayRouteName = array('subjects', 'courses', 'academics');

        if(!in_array($routeName, $arrayRouteName)){
            return false;
        }
        return true;
    }

    public static function isUsersRoutes($routeName){
        $arrayRouteName = array('teachers', 'students', 'hrs');

        if(!in_array($routeName, $arrayRouteName)){
            return false;
        }
        return true;
    }

    public static function isListPage($routeName){
        $arrayRouteName = array('subjects', 'courses', 'academics', 'criterias', 'students', 'hrs', 'teachers', 'questionnaires');

        if(!in_array($routeName, $arrayRouteName)){
            return false;
        }
        return true;
    }

    public static function removeAvatarsNotExistOnDatabase($model, $field) {
        $existingImages = $model->pluck($field)->all();
        // $avatarDirectory = storage_path('app/public/public/avatars');
        $avatarDirectory = storage_path('public/avatars');
        $filesInDirectory = scandir($avatarDirectory);

        foreach ($filesInDirectory as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = 'public/avatars/' . $file;
                if (!in_array($filePath, $existingImages)) {
                    unlink(storage_path('app/public/' . $filePath));
                }
            }
        }
    }

    public static function removeTeacherAvatarsNotExistOnDatabase($model, $field) {
        $existingImages = $model->pluck($field)->all();
        $avatarDirectory = storage_path('app/public/public/teachers/avatars');
        $filesInDirectory = scandir($avatarDirectory);

        foreach ($filesInDirectory as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = 'public/teachers/avatars/' . $file;
                if (!in_array($filePath, $existingImages)) {
                    unlink(storage_path('app/public/' . $filePath));
                }
            }
        }
    }


    // PRODUCTION
    public static function removeAvatarsNotExistOnDatabaseProd($model, $field) {
        $existingImages = $model->pluck($field)->all();
        $avatarDirectory = storage_path('avatars');
        $filesInDirectory = scandir($avatarDirectory);

        foreach ($filesInDirectory as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = 'avatars/' . $file;
                if (!in_array($filePath, $existingImages)) {
                    unlink(storage_path($filePath));
                }
            }
        }
    }

    public static function removeTeacherAvatarsNotExistOnDatabaseProd($model, $field) {
        $existingImages = $model->pluck($field)->all();
        $avatarDirectory = storage_path('teachers/avatars');
        $filesInDirectory = scandir($avatarDirectory);

        foreach ($filesInDirectory as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = 'avatars/' . $file;
                if (!in_array($filePath, $existingImages)) {
                    unlink(storage_path('teachers/' . $filePath));
                }
            }
        }
    }
}

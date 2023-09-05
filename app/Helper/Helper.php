<?php

namespace App\Helper;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class Helper
{
    public static function showEnvironment(){
        if (!App::environment(['local', 'staging']) || !app()->environment(['local', 'staging'])) {
            $env = 'Teacher Evaluation System';
        } else {
            $env = 'LOCAL';
        }
        return $env;
    }

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
            } else {
                return false;
            }
        }
    }

    public static function otpPrivateNumberFormat($phone_number)
    {
        return preg_replace('/\d{8}(\d{3})/', '******$1', $phone_number);
    }

    public static function isAccessingPrivateUrl($session)
    {
        if (!$session) {
            abort(404, 'Resource Not Found');
        }
    }

    public static function isAcaddemicFocusRoutes($routeName)
    {
        $arrayRouteName = array('subjects', 'courses', 'academics');

        if (!in_array($routeName, $arrayRouteName)) {
            return false;
        }
        return true;
    }

    public static function isUsersRoutes($routeName)
    {
        $arrayRouteName = array('teachers', 'students', 'hrs');

        if (!in_array($routeName, $arrayRouteName)) {
            return false;
        }
        return true;
    }

    public static function isListPage($routeName)
    {
        $arrayRouteName = array('subjects', 'courses', 'academics', 'criterias', 'students', 'hrs', 'teachers', 'questionnaires');

        if (!in_array($routeName, $arrayRouteName)) {
            return false;
        }
        return true;
    }

    public static function removeAvatarsNotExistOnDatabase($model, $field)
    {
        if (!App::environment(['local', 'staging']) || !app()->environment(['local', 'staging'])) {
            $storagePath = ($field === 'teachersAvatar' ? 'app/public/public/teachers/avatars/' : 'app/public/public/avatars/');
        } else {
            $storagePath = ($field === 'teachersAvatar' ? 'app/public/public/teachers/avatars/' : 'app/public/public/avatars/');
        }

        $existingImages = $model->pluck($field)->all();
        $avatarDirectory = storage_path($storagePath);
        $filesInDirectory = scandir($avatarDirectory);
        foreach ($filesInDirectory as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $storagePath . $file;
                if (!in_array($file, $existingImages)) {
                    unlink(storage_path($filePath));
                }
            }
        }
    }

    // PRODUCTION
    public static function avatarPathOnProduction($userAvatar, $field)
    {
        if (!App::environment(['local', 'staging']) || !app()->environment(['local', 'staging'])) {
            $path = ($field === 'teachersAvatar' ? 'storage/teachers/avatars/' . $userAvatar : 'storage/avatars/' . $userAvatar);
        } else {
            $path = ($field === 'teachersAvatar' ? 'storage/public/teachers/avatars/' . $userAvatar : 'storage/public/avatars/' . $userAvatar);
            
        }
        return $path;
    }
}

<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\SecurityQuestion;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class Authentication extends Controller
{
    public function loginSecurityQuestion(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            session(['loginData' => $request->all()]);
            session(['loginSecurityQuestionTimeLimit' => now()->addMinutes(2)]);

            return redirect()->route('login.security.question');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    public function showLoginSecurityQuestion()
    {
        if (Helper::isExpired('loginSecurityQuestionTimeLimit')) {
            Session::flush();
            return redirect()->route('welcome')->with('info', 'You took too long on security question. Please start again.');
        }

        $loginData = session('loginData');
        Helper::isAccessingPrivateUrl($loginData);
        $user = User::where('email', $loginData['email'])->with('securityQuestionsAndAnswer')->first();
        if (!$user) {
            return redirect()->route('welcome')->with('error', 'something went wrong.');
        }
        $securityQuestion = $user->securityQuestionsAndAnswer->pluck('question')->toArray();
        $securityQuestionsString = implode(', ', $securityQuestion);

        return view('auth.login-security-question', compact('securityQuestionsString'));
    }

    public function postLoginSecurityQuestion(Request $request)
    {
        $request->validate([
            'security_answer' => ['required', 'string'],
        ]);

        $loginData = session('loginData');
        Helper::isAccessingPrivateUrl($loginData);
        $user = User::where('email', $loginData['email'])->first();
        if ($user && Crypt::decrypt($user->securityAnswer) === $request->security_answer) {
            $phoneNumber = $user->contactNumber;
            $otp = mt_rand(100000, 999999); // Generate a random 6-digit OTP
            if (!Helper::sendOtp($phoneNumber, $otp)) {
                return back()->with('error', 'An error occurred while processing the request.');
            }
            session(['loginSecurityOtpTimeLimit' => now()->addMinutes(5)]);
            $setting = Setting::first();
            if ($setting->smsMode != 0) {
                return redirect()->route('login.otp.security')->with('success', 'Your one-time passcode has been sent to your number. Please check.');
            }
            return redirect()->route('login.otp.security')->with('success', 'Temporary OTP: ' . $otp);
        }

        return back()->with('error', 'Security answer is invalid.');
    }

    public function showOtpPage()
    {
        $otpData = session('smsGatewayData');
        Helper::isAccessingPrivateUrl($otpData);
        $recipientNumber = Helper::otpPrivateNumberFormat($otpData['phoneNumber']);
        $isTimeReached = false;
        if (Helper::isExpired('loginSecurityOtpTimeLimit')) {
            unset($otpData['otp']);
            $isTimeReached = true;
        }
        return view('auth.login-otp-security', compact(['recipientNumber', 'isTimeReached']));
    }

    public function verifyOtp(Request $request)
    {
        $combinedOTP = implode('', $request->otp);
        $otpCode = session('smsGatewayData');
        Helper::isAccessingPrivateUrl($otpCode);
        if (Helper::isExpired('loginSecurityOtpTimeLimit')) {
            return back()->with('info', 'The One-Time Password has reached its expiration period.');
        }

        if (strlen($combinedOTP) === 6 && ctype_digit($combinedOTP) && $otpCode['otp'] == intval($combinedOTP)) {

            $loginData = session('loginData');

            $credentials = [
                'email' => $loginData['email'],
                'password' => $loginData['password']
            ];

            if (Auth::attempt($credentials)) {
                Session::forget(['loginSecurityOtpTimeLimit', 'loginSecurityQuestionTimeLimit', 'loginData']); // burahin lahat ng session na ginamit sa security login bago pumasok sa dashboard
                return redirect('/dashboard')->with('success', 'Welcome ' . Auth::user()->name);
            }
        }
        return back()->with('error', 'The One-Time Password is invalid.');
    }

    public function resendOTP()
    {
        $otp = mt_rand(100000, 999999); // mag giginerate mg 6 digit OTP random number
        $otpData = session('smsGatewayData');
        Helper::isAccessingPrivateUrl($otpData);
        $recipientNumber = $otpData['phoneNumber'];
        if (!Helper::sendOtp($recipientNumber, $otp)) {
            return back()->with('error', 'An error occurred while processing the request.');
        }
        session(['loginSecurityOtpTimeLimit' => now()->addMinutes(5)]);
        $setting = Setting::first();
        if ($setting->smsMode != 0) {
            return redirect()->route('login.otp.security')->with('success', 'Your one-time passcode has been sent to your number. Please check.');
        }
        return redirect()->route('login.otp.security')->with('success', 'Temporary OTP: ' . $otp);
    }
}

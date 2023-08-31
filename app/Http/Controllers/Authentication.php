<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\SecurityQuestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
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
            session(['loginSecurityQuestionTimeLimit' => now()]);

            return redirect()->route('login.security.question');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    public function showLoginSecurityQuestion()
    {
        $loginSecurityQuestionTimeLimit = Session::get('loginSecurityQuestionTimeLimit');
        if ($loginSecurityQuestionTimeLimit && now()->diffInMinutes($loginSecurityQuestionTimeLimit) > 1) {
            Session::flush();
            return redirect()->route('welcome')->with('info', 'You took too long on security question. Please start again.');
        }

        $loginData = session('loginData');
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
        $user = User::where('email', $loginData['email'])->first();
        if ($user && Crypt::decrypt($user->securityAnswer) === $request->security_answer) {

            $phoneNumber = $user->contactNumber;
            $otp = mt_rand(100000, 999999); // Generate a random 6-digit OTP
            session(['smsGatewayData' => array(
                'otp' => $otp,
                'phoneNumber' => $user->contactNumber
            )]);

            Helper::sendOtp($phoneNumber, $otp);
            return redirect()->route('login.otp.security')->with('success', 'Temporary OTP: ' . $otp);
        }

        return back()->with('error', 'Security answer is invalid.');
    }

    public function showOtpPage()
    {
        $otpCode = session('smsGatewayData');
        $recipientNumber = $otpCode['phoneNumber'];
        $loginSecurityQuestionTimeLimit = Session::get('loginSecurityQuestionTimeLimit');
        if ($loginSecurityQuestionTimeLimit && now()->diffInMinutes($loginSecurityQuestionTimeLimit) > 2) {
            Session::flush();
            return redirect()->route('welcome')->with('info', 'The One-Time Password has reached its expiration period.');
        }
        return view('auth.login-otp-security', compact('recipientNumber'));
    }

    public function verifyOtp(Request $request)
    {
        $combinedOTP = implode('', $request->otp);
        $otpCode = session('smsGatewayData');

        if (strlen($combinedOTP) === 6 && ctype_digit($combinedOTP) && $otpCode['otp'] == intval($combinedOTP)) {
            $loginData = session('loginData');
            $credentials = [
                'email' => $loginData['email'],
                'password' => $loginData['password']
            ];

            if (Auth::attempt($credentials)) {
                return redirect('/dashboard')->with('success', 'Welcome ' . Auth::user()->name);
            }
        } else {
            return back()->with('error', 'The One-Time Password is invalid.');
        }
    }
}

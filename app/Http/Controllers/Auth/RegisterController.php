<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityQuestion;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function registrationFirst(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'idNumber' => ['required', 'regex:/^\d{3}-\d{3}-\d{3}$/'],
            'contactNumber' => ['required', 'numeric', 'regex:/^0\d{10}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        session(['registrationData' => $request->all()]);
        session(['registerSecurityQuestionTimeLimit' => now()]);

        return redirect()->route('register.security.question');
    }

    public function showRegistrationSecurityQuestion()
    {
        $registerSecurityQuestionTimeLimit = Session::get('registerSecurityQuestionTimeLimit');
        if ($registerSecurityQuestionTimeLimit && now()->diffInMinutes($registerSecurityQuestionTimeLimit) > 1) {
            return redirect()->route('register')->with('info', 'You took too long on security question. Please start again from registration.');
        }
        return view('auth.register-security-question', ['registerSecurityQuestions' => SecurityQuestion::all()]);
    }

    public function postRegistrationSecurityQuestion(Request $request)
    {
        $request->validate([
            'security_question' => 'required|exists:security_questions,id',
            'security_answer' => 'required|string'
        ]);

        session(['registerSecurityQuestions' => $request->all()]);
        return redirect()->route('register.avatar.upload');
    }

    public function showRegistrationAvatarUpload() {

        return view('auth.register-avatar-upload');
    }

    public function postRegistrationAvatarUpload(Request $request) {

        $request->validate([
            'avatarUrl' => 'required|image|mimes:jpg,png|max:2048',
        ]);

        $avatarName = uniqid().'.'.$request->avatarUrl->extension();
        $avatarPathUrl = $request->avatarUrl->storeAs('public/avatars', $avatarName);
        if(!$avatarPathUrl){
            return back()->with('error', 'An error occured.');
        }

        $registrationData = session('registrationData');
        $sessionSecurityQuestionAndAnswer = session('registerSecurityQuestions');
        $user = User::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'password' => Hash::make($registrationData['password']),
            'idNumber' => $registrationData['idNumber'],
            'contactNumber' => $registrationData['contactNumber'],
            'securityAnswer' => Crypt::encrypt($sessionSecurityQuestionAndAnswer['security_answer']),
            'avatarUrl' => $avatarPathUrl
        ]);

        
        $securityQuestion = SecurityQuestion::find($sessionSecurityQuestionAndAnswer['security_question']);
        $user->securityQuestionsAndAnswer()->create([
            'question' => $securityQuestion->question,
            'answer' => $sessionSecurityQuestionAndAnswer['security_answer'],
        ]);
        
        Session::forget(['registerSecurityQuestionTimeLimit', 'registrationData', 'registerSecurityQuestions']);
        return redirect()->route('welcome')->with('success', 'Account registration received; login possible upon admin approval');
    }
}

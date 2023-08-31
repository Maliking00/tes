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
            'security_question' => ['required', 'exists:security_questions,id'],
            'security_answer' => ['required', 'string'],
        ]);

        $registrationData = session('registrationData');
        $user = User::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'password' => Hash::make($registrationData['password']),
            'idNumber' => $registrationData['idNumber'],
            'contactNumber' => $registrationData['contactNumber'],
            'securityAnswer' => Crypt::encrypt($request->security_answer)
        ]);

        $securityQuestion = SecurityQuestion::find($request->input('security_question'));
        $user->securityQuestionsAndAnswer()->create([
            'question' => $securityQuestion->question,
            'answer' => $request->security_answer,
        ]);

        return redirect()->route('welcome')->with('success', 'Account registration received; login possible upon admin approval'); 
    }
}

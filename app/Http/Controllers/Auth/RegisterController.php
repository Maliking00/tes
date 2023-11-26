<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Courses;
use App\Models\SecurityQuestion;
use App\Models\Subjects;
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
    // use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $courses = Courses::all();
        $subjects = Subjects::all();
        if ($courses->count() === 0 || $subjects->count() === 0) {
            return back()->with('info', 'Registration is temporarily closed.');
        }
        return view('auth.register', compact(['courses', 'subjects']));
    }

    public function registrationFirst(Request $request)
    {
        // validate all field 
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'idNumber' => 'required|regex:/^\d{10}$/',
                'courses' => 'required|exists:courses,id',
                'contactNumber' => [
                    'required',
                    'numeric',
                    'regex:/^09\d{9}$/'
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                    'confirmed'
                ],
                'subjects' => 'required|array',
                'subjects.*' => 'exists:subjects,id'
            ],
            [
                'password.regex' => 'Please make sure your password includes at least one uppercase letter, one lowercase letter, one digit, and one special character (e.g., @, #, $).',
                'contactNumber.regex' => 'Please ensure that your contact number starts with "09" and consists of exactly 11 digits.',
            ]
        );

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

        $registerSecurityQuestions = SecurityQuestion::all();
        return view('auth.register-security-question', compact('registerSecurityQuestions'));
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

    public function showRegistrationAvatarUpload()
    {

        return view('auth.register-avatar-upload');
    }

    public function postRegistrationAvatarUpload(Request $request)
    {

        $request->validate([
            'avatarUrl' => 'required|image|mimes:jpg,png|max:2048',
        ]);

        $avatarName = uniqid() . '.' . $request->avatarUrl->extension();
        $request->avatarUrl->storeAs('public/avatars', $avatarName);

        $registrationData = session('registrationData');
        $sessionSecurityQuestionAndAnswer = session('registerSecurityQuestions');
        $user = User::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'password' => Hash::make($registrationData['password']),
            'idNumber' => $registrationData['idNumber'],
            'contactNumber' => $registrationData['contactNumber'],
            'securityAnswer' => Crypt::encrypt($sessionSecurityQuestionAndAnswer['security_answer']),
            'avatarUrl' => $avatarName,
            'course_id' => $registrationData['courses']
        ]);

        foreach ($registrationData['subjects'] ?? [] as $subjectID) {
            $user->subjects()->create([
                'subjectID' => $subjectID,
            ]);
        }

        $securityQuestion = SecurityQuestion::find($sessionSecurityQuestionAndAnswer['security_question']);
        $user->securityQuestionsAndAnswer()->create([
            'question' => $securityQuestion->question,
            'answer' => $sessionSecurityQuestionAndAnswer['security_answer'],
        ]);

        Session::forget(['registerSecurityQuestionTimeLimit', 'registrationData', 'registerSecurityQuestions']);
        return redirect()->route('welcome')->with('success', 'Account registration received; login possible upon admin approval');
    }
}

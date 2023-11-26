<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityQuestion;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        $admin = User::find(Auth::id());
        $setting = Setting::first();
        $securityQuestionsString = SecurityQuestion::all();
        $defaultSecurityQA = $admin->securityQuestionsAndAnswer->first();
        return view('settings', compact(['setting', 'securityQuestionsString', 'defaultSecurityQA']));
    }

    public function updateAdmin($id, Request $request, User $userModel)
    {
        $adminCreds = $userModel->findOrFail($id);
        $validate = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $adminCreds->id,
                'idNumber' => 'required|regex:/^\d{10}$/',
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
                ],
                'security_question' => 'required|exists:security_questions,id',
                'security_answer' => 'required|string',
            ],
            [
                'password.regex' => 'Please make sure your password includes at least one uppercase letter, one lowercase letter, one digit, and one special character (e.g., @, #, $).',
                'contactNumber.regex' => 'Please ensure that your contact number starts with "09" and consists of exactly 11 digits.',
            ]
        );

        if ($request->password === '********') {
            $adminCreds->update([
                'name' => $validate['name'],
                'email' => $validate['email'],
                'idNumber' => $validate['idNumber'],
                'contactNumber' => $validate['contactNumber'],
                'securityAnswer' => Crypt::encrypt($validate['security_answer'])
            ]);
        } else {
            $adminCreds->update([
                'name' => $validate['name'],
                'email' => $validate['email'],
                'password' => Hash::make($request->password),
                'idNumber' => $validate['idNumber'],
                'contactNumber' => $validate['contactNumber'],
                'securityAnswer' => Crypt::encrypt($validate['security_answer'])
            ]);
        }

        $securityQuestion = SecurityQuestion::find($request->security_question);
        $adminCreds->securityQuestionsAndAnswer()->update([
            'question' => $securityQuestion->question,
            'answer' => $request->security_answer
        ]);

        return back()->with('success', $validate['name'] . ' successfully updated.');
    }

    public function updateSetting(Request $request, Setting $settingModel)
    {
        $validatedData = $request->validate([
            'semaphoreApiKey' => 'required|string',
            'weatherCity' => 'required|string',
        ]);

        $validatedData['smsMode'] = $request->has('smsMode') ? 0 : 1;

        $settingModel = Setting::firstOrNew([]);
        $settingModel->fill($validatedData);
        $settingModel->save();

        return redirect()->route('settings')->with('success', 'Settings updated successfully');
    }
}

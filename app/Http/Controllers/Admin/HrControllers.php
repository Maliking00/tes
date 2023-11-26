<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Models\SecurityQuestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class HrControllers extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        $hrSecurityQuestions = SecurityQuestion::all();
        return view('hrs.hrs', compact('hrSecurityQuestions'));
    }

    public function loadHrs(Request $request, User $userModel)
    {
        $input = $request->search_input;
        $search = $userModel->newQuery();
        $search->where(function ($query) use ($input) {
            $query->where('name', 'like', "%{$input}%")->orWhere('email', 'like', "%{$input}%")->orWhere('idNumber', 'like', "%{$input}%")->orWhere('contactNumber', 'like', "%{$input}%")->orWhere('status', 'like', "%{$input}%");
        })->where('role', 'HR');
        $hrs = $search->orderBy('updated_at', 'DESC')->paginate(6);
        if ($hrs->count() > 0) {
            $html = '<table class="table">
            <thead>
            <tr class="t-row-head" data-aos="fade-up" data-aos-delay="100">
                <th>Name</th>
                <th>ID Number</th>
                <th>Status</th>
                <th>Last Update</th>
                <th></th>
            </tr>
            </thead>
            <tbody>';

            $delay = 1;
            foreach ($hrs as $hr) {
                $delay++;
                $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <td class="d-flex align-items-center gap-3">
                            <img src="' . asset(Helper::avatarPathOnProduction($hr->avatarUrl, 'avatarUrl')) . '" alt="' . $hr->name . '"/>
                            <div>
                            <p>' . $hr->name . '</p>
                            <p style="color: #1376da;">' . $hr->email . '</p>
                            </div>
                        </td>
                        <td class="text-capitalize">' . $hr->idNumber . '</td>
                        <td class="text-capitalize">' . ($hr->status == 'pending' ? '<span class="badge bg-primary">Pending</span>' : '<span class="badge bg-success">Approved</span>') . '</td>
                        <td class="text-lowercase">' . $hr->updated_at->diffForHumans() . '</td>
                        <td>
                        <div class="dropup">
                            <a href="' . route('show.edit.hr', $hr->id) . '"><i class="ti-angle-double-right show-options"></i></a>
                        </div>
                        </td>
                    </tr>';
            }
            $html .= '</tbody>
            </table>';
        } else {
            $html = '<div class="v-100 text-center" data-aos="fade-up" data-aos-delay="400">
                        <div class="card">
                            <div class="card-body">
                                <img class="img-fluid" src="' . asset('/assets/images/404.jpg') . '" alt="Not found">
                                <h3 class="font-weight-normal mt-4">No Hr found</h3>
                                <p>I\'m sorry, but the specified hr could not be found.</p>
                                <p>Please provide additional details or clarify your request for further assistance.</p>
                            </div>
                        </div>
                    </div>';
        }

        return response()->json([
            'table' => $html,
            'pagination' => $hrs
        ], 200);
    }

    public function storeHr(Request $request, User $userModel)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
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
                'avatar' => 'required|image|mimes:jpg,png|max:2048',
            ],
            [
                'password.regex' => 'Please make sure your password includes at least one uppercase letter, one lowercase letter, one digit, and one special character (e.g., @, #, $).',
                'contactNumber.regex' => 'Please ensure that your contact number starts with "09" and consists of exactly 11 digits.',
            ]
        );

        $avatarName = uniqid() . '.' . $request->avatar->extension();

        $hr = $userModel->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'idNumber' => $request->idNumber,
            'contactNumber' => $request->contactNumber,
            'securityAnswer' => Crypt::encrypt($request->security_answer),
            'role' => 'HR',
            'status' => 'approved',
            'avatarUrl' => $avatarName
        ]);

        Helper::removeAvatarsNotExistOnDatabase('avatarUrl', $hr->avatarUrl);
        $request->avatar->storeAs('public/avatars', $hr->avatarUrl);
        if ($hr) {

            $securityQuestion = SecurityQuestion::find($request->security_question);
            $hr->securityQuestionsAndAnswer()->create([
                'question' => $securityQuestion->question,
                'answer' => $request->security_answer
            ]);

            return response()->json([
                'success' => $request->name . ' successfully added.',
            ], 200);
        }
    }

    public function showEditHr($id, User $userModel)
    {
        $hr = $userModel->findOrFail($id);
        $securityQuestionsString = SecurityQuestion::all();
        $defaultSecurityQA = $hr->securityQuestionsAndAnswer->first();
        $hrID = $hr->id;
        return view('hrs.edit-hrs', compact(['hr', 'securityQuestionsString', 'defaultSecurityQA']));
    }

    public function updateHr($id, Request $request, User $userModel)
    {
        $hrs = $userModel->findOrFail($id);
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $hrs->id,
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
                'security_answer' => 'required|string'
            ],
            [
                'password.regex' => 'Please make sure your password includes at least one uppercase letter, one lowercase letter, one digit, and one special character (e.g., @, #, $).',
                'contactNumber.regex' => 'Please ensure that your contact number starts with "09" and consists of exactly 11 digits.',
            ]
        );

        if ($request->password === '********') {
            $hrs->update([
                'name' => $request->name,
                'email' => $request->email,
                'idNumber' => $request->idNumber,
                'contactNumber' => $request->contactNumber,
                'securityAnswer' => Crypt::encrypt($request->security_answer),
            ]);
        } else {
            $hrs->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'idNumber' => $request->idNumber,
                'contactNumber' => $request->contactNumber,
                'securityAnswer' => Crypt::encrypt($request->security_answer),
            ]);
        }

        $securityQuestion = SecurityQuestion::find($request->security_question);
        $hrs->securityQuestionsAndAnswer()->update([
            'question' => $securityQuestion->question,
            'answer' => $request->security_answer
        ]);

        return back()->with('success', 'Hrs successfully updated.');
    }

    public function updateHrRole($id, Request $request, User $userModel)
    {
        $user = $userModel->findOrFail($id);
        $validatedData = $request->validate(
            [
                'role' => 'in:student,HR'
            ],
            [
                'role.in' => 'Roles is invalid.'
            ]
        );

        if (!$user->update($validatedData)) {
            return back()->with('error', 'An error occurred.');
        }
        return back()->with('success', 'Role successfully updated.');
    }

    public function updateHrStatus($id, Request $request, User $userModel)
    {
        $user = $userModel->findOrFail($id);
        $validatedData = $request->validate(
            [
                'status' => 'in:pending,approved'
            ],
            [
                'status.in' => 'Status is invalid.'
            ]
        );

        if (!$user->update($validatedData)) {
            return back()->with('error', 'An error occurred.');
        }
        return redirect()->route('students')->with('success', 'Status successfully updated.');
    }

    public function updateHrAvatar($id, Request $request, User $userModel)
    {
        $user = $userModel->findOrFail($id);
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,png|max:2048',
        ]);

        $avatarName = uniqid() . '.' . $request->avatar->extension();
        Helper::removeAvatarsNotExistOnDatabase('avatarUrl', $user->avatarUrl);

        if (!$user->update(['avatarUrl' => $avatarName])) {
            return back()->with('error', 'An error occurred.');
        }
        $request->avatar->storeAs('public/avatars', $user->avatarUrl);
        return back()->with('success', 'Avatar successfully updated.');
    }

    public function deleteHr($id, User $userModel)
    {
        $student = $userModel->findOrFail($id);
        if (!$student->delete()) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('hrs')->with('success', 'Student successfully deleted.');
    }
}

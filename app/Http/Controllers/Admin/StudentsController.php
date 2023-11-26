<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Models\Courses;
use App\Models\SecurityQuestion;
use App\Models\StudentSubject;
use App\Models\Subjects;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class StudentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        $studentSecurityQuestions = SecurityQuestion::all();
        $courses = Courses::all();
        $subjects = Subjects::all();
        if ($courses->count() === 0) {
            return redirect()->route('courses')->with('warning', 'Please add a courses before adding a student.');
        }
        if ($subjects->count() === 0) {
            return redirect()->route('subjects')->with('warning', 'Please add a subjects before adding a student.');
        }
        return view('students.students', compact(['studentSecurityQuestions', 'courses', 'subjects']));
    }

    public function loadStudents(Request $request, User $userModel)
    {
        $input = $request->search_input;
        $search = $userModel->newQuery();
        $search->where(function ($query) use ($input) {
            $query->where('name', 'like', "%{$input}%")->orWhere('email', 'like', "%{$input}%")->orWhere('idNumber', 'like', "%{$input}%")->orWhere('contactNumber', 'like', "%{$input}%")->orWhere('status', 'like', "%{$input}%");
        })->where('role', 'student');
        $students = $search->orderBy('updated_at', 'DESC')->paginate(6);
        if ($students->count() > 0) {
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
            foreach ($students as $student) {
                $delay++;
                $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <td class="d-flex align-items-center gap-3">
                            <img src="' . asset(Helper::avatarPathOnProduction($student->avatarUrl, 'avatarUrl')) . '" alt="' . $student->name . '"/>
                            <div>
                            <p>' . $student->name . '</p>
                            <p style="color: #1376da;">' . $student->email . '</p>
                            </div>
                        </td>
                        <td class="text-capitalize">' . $student->idNumber . '</td>
                        <td class="text-capitalize">' . ($student->status == 'pending' ? '<span class="badge bg-primary">Pending</span>' : '<span class="badge bg-success">Approved</span>') . '</td>
                        <td class="text-lowercase">' . $student->updated_at->diffForHumans() . '</td>
                        <td>
                        <div class="dropup">
                            <a href="' . route('show.edit.student', $student->id) . '"><i class="ti-angle-double-right show-options"></i></a>
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
                                <h3 class="font-weight-normal mt-4">No Student found</h3>
                                <p>I\'m sorry, but the specified student could not be found.</p>
                                <p>Please provide additional details or clarify your request for further assistance.</p>
                            </div>
                        </div>
                    </div>';
        }

        return response()->json([
            'table' => $html,
            'pagination' => $students
        ], 200);
    }



    public function storeStudent(Request $request, User $userModel)
    {
        $validate = $request->validate(
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
                'courses' => 'required|exists:courses,id',
                'subjects' => 'required|array',
                'subjects.*' => 'exists:subjects,id'
            ],
            [
                'password.regex' => 'Please make sure your password includes at least one uppercase letter, one lowercase letter, one digit, and one special character (e.g., @, #, $).',
                'contactNumber.regex' => 'Please ensure that your contact number starts with "09" and consists of exactly 11 digits.',
            ]
        );

        $avatarName = uniqid() . '.' . $request->avatar->extension();

        $student = $userModel->create([
            'name' => $validate['name'],
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
            'idNumber' => $validate['idNumber'],
            'contactNumber' => $validate['contactNumber'],
            'securityAnswer' => Crypt::encrypt($validate['security_answer']),
            'role' => 'student',
            'status' => 'approved',
            'avatarUrl' => $avatarName,
            'course_id' => $validate['courses'],
        ]);

        Helper::removeAvatarsNotExistOnDatabase('avatarUrl', $student->avatarUrl);
        $request->avatar->storeAs('public/avatars', $student->avatarUrl);

        if ($student) {
            foreach ($request->input('subjects', []) as $subjectID) {
                $student->subjects()->create([
                    'subjectID' => $subjectID,
                ]);
            }

            $securityQuestion = SecurityQuestion::find($request->security_question);
            $student->securityQuestionsAndAnswer()->create([
                'question' => $securityQuestion->question,
                'answer' => $request->security_answer
            ]);

            return response()->json([
                'success' => $request->name . ' successfully added.',
            ], 200);
        }
    }

    public function showEditStudent($id, User $userModel)
    {
        $student = $userModel->findOrFail($id);
        $securityQuestionsString = SecurityQuestion::all();
        $defaultSecurityQA = $student->securityQuestionsAndAnswer->first();
        $studentID = $student->id;
        $courses = Courses::all();
        $subjects = Subjects::all();
        $defaultCourse = $student->course_id;
        $defaultSubject = $student->subjects()->get();
        return view('students.edit-students', compact(['student', 'securityQuestionsString', 'defaultSecurityQA', 'courses', 'defaultCourse', 'subjects', 'defaultSubject']));
    }

    public function updateStudent($id, Request $request, User $userModel)
    {
        $students = $userModel->findOrFail($id);
        $validate = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $students->id,
                'idNumber' => 'required|regex:/^\d{10}$/',
                'contactNumber' => [
                    'required',
                    'numeric',
                    'regex:/^09\d{9}$/'
                ],
                'courses' => 'required|exists:courses,id',
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
                'subjects' => 'required|array',
                'subjects.*' => 'exists:subjects,id'
            ],
            [
                'password.regex' => 'Please make sure your password includes at least one uppercase letter, one lowercase letter, one digit, and one special character (e.g., @, #, $).',
                'contactNumber.regex' => 'Please ensure that your contact number starts with "09" and consists of exactly 11 digits.',
            ]
        );

        if ($request->password === 'ThisIsAPassWord@123') {
            $students->update([
                'name' => $validate['name'],
                'email' => $validate['email'],
                'idNumber' => $validate['idNumber'],
                'contactNumber' => $validate['contactNumber'],
                'course_id' => $validate['courses'],
                'securityAnswer' => Crypt::encrypt($validate['security_answer'])
            ]);
        } else {
            $students->update([
                'name' => $validate['name'],
                'email' => $validate['email'],
                'password' => Hash::make($request->password),
                'idNumber' => $validate['idNumber'],
                'contactNumber' => $validate['contactNumber'],
                'course_id' => $validate['courses'],
                'securityAnswer' => Crypt::encrypt($validate['security_answer'])
            ]);
        }

        $subjectsToDelete  = $students->subjects->pluck('subjectID')->toArray();
        StudentSubject::whereIn('subjectID', $subjectsToDelete)->where('studentID', $students->id)->delete();

        foreach ($validate['subjects'] ?? [] as $subjectID) {
            $students->subjects()->create([
                'subjectID' => $subjectID,
            ]);
        }

        $securityQuestion = SecurityQuestion::find($request->security_question);
        $students->securityQuestionsAndAnswer()->update([
            'question' => $securityQuestion->question,
            'answer' => $request->security_answer
        ]);

        return back()->with('success', 'Students successfully updated.');
    }

    public function updateStudentRole($id, Request $request, User $userModel)
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

    public function updateStudentStatus($id, Request $request, User $userModel)
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

    public function updateStudentAvatar($id, Request $request, User $userModel)
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

    public function deleteStudent($id, User $userModel)
    {
        $student = $userModel->findOrFail($id);
        if (!$student->delete()) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('students')->with('success', 'Student successfully deleted.');
    }
}

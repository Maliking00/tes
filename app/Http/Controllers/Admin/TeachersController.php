<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Models\Teachers;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        return view('teachers.teachers');
    }

    public function loadTeachers(Request $request, Teachers $teacherModel)
    {
        $input = $request->search_input;
        $search = $teacherModel->newQuery();
        $search->where(function ($query) use ($input) {
            $query->where('teachersFullName', 'like', "%{$input}%")->orWhere('teachersIdNumber', 'like', "%{$input}%")->orWhere('teachersContactNumber', 'like', "%{$input}%")->orWhere('teachersEmail', 'like', "%{$input}%");
        });
        $teachers = $search->orderBy('updated_at', 'DESC')->paginate(6);
        if ($teachers->count() > 0) {
            $html = '<table class="table">
            <thead>
            <tr class="t-row-head" data-aos="fade-up" data-aos-delay="100">
                <th>Name</th>
                <th>ID Number</th>
                <th>Last Update</th>
                <th></th>
            </tr>
            </thead>
            <tbody>';

            $delay = 1;
            foreach ($teachers as $teacher) {
                $delay++;
                $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <td class="d-flex align-items-center gap-3">
                            <img src="' . asset(Helper::avatarPathOnProduction($teacher->teachersAvatar, 'teachersAvatar')) . '" alt="' . $teacher->teachersFullName . '"/>
                            <div>
                            <p>' . $teacher->teachersFullName . '</p>
                            <p style="color: #1376da;">' . $teacher->teachersEmail . '</p>
                            </div>
                        </td>
                        <td class="text-capitalize">' . $teacher->teachersIdNumber . '</td>
                        <td class="text-lowercase">' . $teacher->updated_at->diffForHumans() . '</td>
                        <td>
                        <div class="dropup">
                            <a href="' . route('show.edit.teacher', $teacher->id) . '"><i class="ti-angle-double-right show-options"></i></a>
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
                                <h3 class="font-weight-normal mt-4">No Teacher found</h3>
                                <p>I\'m sorry, but the specified teacher could not be found.</p>
                                <p>Please provide additional details or clarify your request for further assistance.</p>
                            </div>
                        </div>
                    </div>';
        }

        return response()->json([
            'table' => $html,
            'pagination' => $teachers
        ], 200);
    }

    public function storeTeacher(Request $request, Teachers $teacherModel)
    {
        $request->validate([
            'teachersFullName' => 'required|string|max:255',
            'teachersEmail' => 'required|string|email|max:255|unique:teachers',
            'teachersIdNumber' => 'required|regex:/^\d{3}-\d{3}-\d{3}$/',
            'teachersContactNumber' => 'required|numeric|regex:/^0\d{10}$/',
            'teachersAvatar' => 'required|image|mimes:jpg,png|max:2048',
        ]);

        $avatarName = uniqid() . '.' . $request->teachersAvatar->extension();

        $teacher = $teacherModel->create([
            'teachersFullName' => $request->teachersFullName,
            'teachersEmail' => $request->teachersEmail,
            'teachersIdNumber' => $request->teachersIdNumber,
            'teachersContactNumber' => $request->teachersContactNumber,
            'teachersAvatar' => $avatarName
        ]);

        Helper::removeAvatarsNotExistOnDatabase('teachersAvatar', $teacher->teachersAvatar);
        $request->teachersAvatar->storeAs('public/teachers/avatars', $teacher->teachersAvatar);

        if ($teacher) {
            return response()->json([
                'success' => $request->name . ' successfully added.',
            ], 200);
        }
    }

    public function showEditTeacher($id, Teachers $teacherModel)
    {
        $teacher = $teacherModel->findOrFail($id);
        return view('teachers.edit-teachers', compact('teacher'));
    }

    public function updateTeacher($id, Request $request, Teachers $teacherModel)
    {
        $teachers = $teacherModel->findOrFail($id);
        $request->validate([
            'teachersFullName' => 'required|string|max:255',
                'teachersEmail' => 'required|string|email|max:255|unique:teachers,teachersEmail,' . $teachers->id,
                'teachersIdNumber' => 'required|regex:/^\d{3}-\d{3}-\d{3}$/|unique:teachers,teachersIdNumber,' . $teachers->id,
                'teachersContactNumber' => 'required|numeric|regex:/^0\d{10}$/'
        ]);

        $teachers->update([
            'teachersFullName' => $request->teachersFullName,
            'teachersEmail' => $request->teachersEmail,
            'teachersIdNumber' => $request->teachersIdNumber,
            'teachersContactNumber' => $request->teachersContactNumber
        ]);

        return redirect()->route('teachers')->with('success', 'Teacher successfully updated.');
    }

    public function updateTeacherAvatar($id, Request $request, Teachers $teacherModel)
    {
        $user = $teacherModel->findOrFail($id);
        $request->validate([
            'teachersAvatar' => 'required|image|mimes:jpg,png|max:2048',
        ]);

        $avatarName = uniqid() . '.' . $request->teachersAvatar->extension();
        Helper::removeAvatarsNotExistOnDatabase('teachersAvatar', $user->teachersAvatar);
        if (!$user->update(['teachersAvatar' => $avatarName])) {
            return back()->with('error', 'An error occurred.');
        }
        $request->teachersAvatar->storeAs('public/teachers/avatars', $avatarName);
        return back()->with('success', 'Avatar successfully updated.');
    }

    public function deleteTeacher($id, Teachers $teacherModel)
    {
        $teacher = $teacherModel->findOrFail($id);
        if (!$teacher->delete()) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('teachers')->with('success', 'Teacher successfully deleted.');
    }
}

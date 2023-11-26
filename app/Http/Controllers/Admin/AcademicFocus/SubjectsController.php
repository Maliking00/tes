<?php

namespace App\Http\Controllers\Admin\AcademicFocus;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Models\Courses;
use App\Models\StudentSubject;
use App\Models\Subjects;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        $isCoursesEmpty = !Courses::query()->exists();
        if ($isCoursesEmpty) {
            return redirect()->route('courses')->with('warning', 'Please add a courses before adding a subject.');
        }
        $courses = Courses::all();
        return view('academicFocus.subjects.subjects', compact('courses'));
    }

    public function loadSubjects(Request $request, Subjects $subjectResult)
    {
        $input = $request->search_input;
        $search = $subjectResult->newQuery();
        $search->where(function ($query) use ($input) {
            $query->where('subjectName', 'like', "%{$input}%")->orWhere('SubjectDescription', 'like', "%{$input}%");
        });
        $subjects = $search->orderBy('updated_at', 'DESC')->paginate(6);
        if ($subjects->count() > 0) {
            $html = '<table class="table">
            <thead>
            <tr class="t-row-head" data-aos="fade-up" data-aos-delay="100">
                <th>Course</th>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Subject Description</th>
                <th>Last Update</th>
                <th></th>
            </tr>
            </thead>
            <tbody>';

            $delay = 1;
            foreach ($subjects as $subject) {
                $delay++;
                $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <td class="text-capitalize">' . $subject->course->courseName . '</td>
                        <td class="text-capitalize">' . $subject->subjectCode . '</td>
                        <td class="text-capitalize">' . Helper::shortenDescription($subject->subjectName, 20) . '</td>
                        <td class="text-capitalize">' . Helper::shortenDescription($subject->subjectDescription, 20) . '</td>
                        <td class="text-lowercase">' . $subject->updated_at->diffForHumans() . '</td>
                        <td>
                        <div class="dropup">
                            <a href="' . route('show.edit.subject', $subject->id) . '"><i class="ti-angle-double-right show-options"></i></a>
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
                                <h3 class="font-weight-normal mt-4">No Subject found</h3>
                                <p>I\'m sorry, but the specified subject could not be found.</p>
                                <p>Please provide additional details or clarify your request for further assistance.</p>
                            </div>
                        </div>
                    </div>';
        }

        return response()->json([
            'table' => $html,
            'pagination' => $subjects
        ], 200);
    }

    public function storeSubject(Request $request, Subjects $subjectModel)
    {
        $validatedData = $request->validate([
            'subjectCode' => 'required|string|unique:subjects',
            'subjectName' => 'required|string',
            'subjectDescription' => 'required|string',
            'course_id' => 'required|string|exists:courses,id',
        ]);
        if ($subjectModel->create($validatedData)) {
            return response()->json([
                'success' => $request->subjectName . ' successfully added.',
            ], 200);
        }
    }

    public function showEditSubject($id, Subjects $subjectModel)
    {
        $subject = $subjectModel->findOrFail($id);
        $courses = Courses::all();
        $defaultCourse = $subject->course_id;
        return view('academicFocus.subjects.edit-subject', compact(['subject', 'courses', 'defaultCourse']));
    }

    public function updateSubject($id, Request $request, Subjects $subjectModel)
    {
        $subject = $subjectModel->findOrFail($id);
        $validatedData = $request->validate([
            'subjectCode' => 'required|string|unique:subjects,subjectCode,' . $subject->id,
            'subjectName' => 'required|string',
            'subjectDescription' => 'required|string',
            'course_id' => 'required|string|exists:courses,id',
        ]);

        $existingSubject = $subjectModel->where('subjectCode', $validatedData['subjectCode'])->where('id', '<>', $id)->first();
        if ($existingSubject) {
            $validatedData['subjectCode'] = $validatedData['subjectCode'] . '-copy';
        }

        if (!$subject->update($validatedData)) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('subjects')->with('success', 'Subject successfully updated.');
    }

    public function deleteSubject($id, Subjects $subjectModel)
    {
        $subject = $subjectModel->findOrFail($id);
        $studentSubject = StudentSubject::where('subjectID', $id)->delete();
        if (!$subject->delete() && !$studentSubject) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('subjects')->with('success', 'Subject successfully deleted.');
    }
}

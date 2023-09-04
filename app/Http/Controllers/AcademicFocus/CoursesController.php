<?php

namespace App\Http\Controllers\AcademicFocus;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Courses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CoursesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        return view('academicFocus.courses.courses');
    }

    public function loadCourses(Request $request, Courses $coursesResult)
    {
        $input = $request->search_input;
        $search = $coursesResult->newQuery();
        $search->where(function ($query) use ($input) {
            $query->where('courseName', 'like', "%{$input}%")->orWhere('courseYearLevel', 'like', "%{$input}%")->orWhere('courseSection', 'like', "%{$input}%");
        });
        $courses = $search->orderBy('updated_at', 'DESC')->paginate(6);
        if ($courses->count() > 0) {
            $html = '<table class="table">
            <thead>
            <tr class="t-row-head" data-aos="fade-up" data-aos-delay="100">
                <th>Course Name</th>
                <th>Year Level</th>
                <th>Section</th>
                <th>Last Update</th>
                <th></th>
            </tr>
            </thead>
            <tbody>';

            $delay = 1;
            foreach ($courses as $course) {
                $delay++;
                $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <td class="text-capitalize">' . $course->courseName . '</td>
                        <td class="text-capitalize">' . $course->courseYearLevel . '</td>
                        <td class="text-capitalize">' . Helper::shortenDescription($course->courseSection, 20) . '</td>
                        <td class="text-lowercase">' . $course->updated_at->diffForHumans() . '</td>
                        <td>
                        <div class="dropup">
                            <a href="' . route('show.edit.course', $course->id) . '"><i class="ti-angle-double-right show-options"></i></a>
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
                                <h3 class="font-weight-normal mt-4">No Courses found</h3>
                                <p>I\'m sorry, but the specified course could not be found.</p>
                                <p>Please provide additional details or clarify your request for further assistance.</p>
                            </div>
                        </div>
                    </div>';
        }

        return response()->json([
            'table' => $html,
            'pagination' => $courses
        ], 200);
    }

    public function storeCourse(Request $request, Courses $courseModel)
    {
        try {
            $validatedData = $request->validate([
                'courseName' => 'required|string',
                'courseYearLevel' => 'required|numeric',
                'courseSection' => 'required|string',
            ]);
            $isNameAndYearAndSectionExist = $courseModel::where('courseName', $validatedData['courseName'])->where('courseYearLevel', $validatedData['courseYearLevel'])->where('courseSection', $validatedData['courseSection'])->first();

            if ($isNameAndYearAndSectionExist) {
                return response()->json([
                    'errors' => array(
                        'courseName' => array(
                            0 => 'Name is exist'
                        ),
                        'courseYearLevel' => array(
                            0 => 'Year level is exist'
                        ),
                        'courseSection' => array(
                            0 => 'Section is exist'
                        ),
                    )
                ], 422);
            }
            if ($courseModel->create($validatedData)) {
                return response()->json([
                    'success' => $request->courseName . ' successfully added.',
                ], 200);
            }
        } catch (ValidationException $e) {
            return new JsonResponse(['errors' => $e->errors()], 422);
        }
    }

    public function showEditCourse($id, Courses $courseModel)
    {
        $course = $courseModel->findOrFail($id);
        return view('academicFocus.courses.edit-courses', compact('course'));
    }

    public function updateCourse($id, Request $request, Courses $courseModel)
    {
        $course = $courseModel->findOrFail($id);
        $validatedData = $request->validate([
            'courseName' => 'required|string',
            'courseYearLevel' => 'required|numeric',
            'courseSection' => 'required|string',
        ]);

        if (!$course->update($validatedData)) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('courses')->with('success', 'Course successfully updated.');
    }

    public function deleteCourse($id, Courses $courseModel)
    {
        $courses = $courseModel->findOrFail($id);
        if (!$courses->delete()) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('courses')->with('success', 'Course successfully deleted.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Academic;
use App\Models\Courses;
use App\Models\Criterias;
use App\Models\EvaluationList;
use App\Models\Subjects;
use App\Models\TeacherEvaluationStatus;
use App\Models\Teachers;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isStudent']);
    }

    public function index()
    {
        $restrictions = DB::table('restrictions')
            ->select([
                'restrictions.id as restrictionID',
                'academics.id as academicID',
                'teachers.id as teacherID',
                'courses.id as courseID',
                'subjects.id as subjectID',
                'teachers.teachersFullName as teacherName',
                'teachers.teachersAvatar as teacherAvatar',
                'courseName',
                'courseYearLevel',
                'courseSection',
                'subjectCode',
                'subjectName',
                'academicYear',
                'academicSemester'
            ])
            ->join('academics', 'academics.id', '=', 'restrictions.academic_id')
            ->join('teachers', 'teachers.id', '=', 'restrictions.teacher_id')
            ->join('subjects', 'subjects.id', '=', 'restrictions.subject_id')
            ->join('courses', 'courses.id', '=', 'restrictions.course_id')
            ->join('student_subjects', 'student_subjects.subjectID', '=', 'subjects.id')
            ->where('restrictions.course_id', Auth::user()->course_id)
            ->where('student_subjects.studentID', Auth::id())
            ->where('academics.academicEvaluationStatus', 'Starting')
            ->orderBy('restrictions.updated_at', 'DESC')
            ->get();

        $academics = Academic::where('academicSystemDefault', 1)->get();
        if ($academics->count() === 0) {
            $currentYear = Carbon::now()->year;
            $lastYear = $currentYear - 1;
            $formattedYears = $lastYear . '-' . $currentYear;

            $isNotCurrentYear = Academic::orderBy('academicSemester', 'DESC')->where('academicYear', $formattedYears)->first();
            if (!empty($isNotCurrentYear)) {
                $academicDefault = $isNotCurrentYear;
            } else {
                $academicDefault = Academic::orderBy('academicSemester', 'DESC')->where('academicYear', '!=', $formattedYears)->first();
            }
        } else {
            $academicDefault = Academic::orderBy('academicSemester', 'DESC')->where('academicSystemDefault', 1)->first();
        }

        return view('evaluations.teachers-evaluation', compact(['restrictions', 'academicDefault']));
    }

    public function evaluateSpecificTeacher($id, $course, $teacherID, $subject_id)
    {
        $academics = Academic::findOrFail($id);
        $restriction = DB::table('restrictions')
            ->select([
                'restrictions.id as restrictionID',
                'academics.id as academicID',
                'teachers.id as teacherID',
                'courses.id as courseID',
                'subjects.id as subjectID',
                'teachers.teachersFullName as teacherName',
                'teachers.teachersAvatar as teacherAvatar',
                'courseName',
                'courseYearLevel',
                'courseSection',
                'subjectCode',
                'subjectName',
                'academicYear',
                'academicSemester',
                DB::raw('UNIX_TIMESTAMP(restrictions.created_at) as dateAssigned')
            ])
            ->join('academics', 'academics.id', '=', 'restrictions.academic_id')
            ->join('teachers', 'teachers.id', '=', 'restrictions.teacher_id')
            ->join('courses', 'courses.id', '=', 'restrictions.course_id')
            ->join('subjects', 'subjects.id', '=', 'restrictions.subject_id')
            ->join('student_subjects', 'student_subjects.subjectID', '=', 'subjects.id')
            ->where('restrictions.course_id', $course)
            ->where('student_subjects.studentID', Auth::id())
            ->where('academics.academicEvaluationStatus', 'Starting')
            ->where('restrictions.teacher_id', $teacherID)
            ->where('restrictions.subject_id', $subject_id)
            ->first();

        $isTeacherEvaluatedFromThisAcademicYear = TeacherEvaluationStatus::where('restriction_id', $restriction->restrictionID)
            ->where('academic_id', $restriction->academicID)
            ->where('course_id', $restriction->courseID)
            ->where('subject_id', $restriction->subjectID)
            ->where('evaluator_id', Auth::id())
            ->exists();

        if ($isTeacherEvaluatedFromThisAcademicYear) {
            return back()->with('info', 'You have evaluate this teacter for this academic year.');
        }
        $criterias = Criterias::get();
        return view('evaluations.teachers-evaluation-form', compact(['restriction', 'criterias']));
    }

    public function storeEvaluateSpecificTeacher(Request $request)
    {
        try {
            $validate = $request->validate([
                'restriction_id' => 'required|string|exists:restrictions,id',
                'academic_id' => 'required|string|exists:academics,id',
                'teacher_id' => 'required|string|exists:teachers,id',
                'course_id' => 'required|string|exists:courses,id',
                'subject_id' => 'required|string|exists:subjects,id',
                'teacher' => 'required|string|exists:teachers,teachersFullName'
            ]);

            $isTeacherEvaluatedFromThisAcademicYear = TeacherEvaluationStatus::where('restriction_id', $validate['restriction_id'])
                ->where('academic_id', $validate['academic_id'])
                ->where('course_id', $validate['course_id'])
                ->where('subject_id', $validate['subject_id'])
                ->where('evaluator_id', Auth::id())
                ->exists();

            if ($isTeacherEvaluatedFromThisAcademicYear) {
                return back()->with('info', 'You have evaluate this Teacher for this academic year.');
            }

            $subject = Subjects::find($validate['subject_id']);
            $course = Courses::find($validate['course_id']);
            $teacher = Teachers::find($validate['teacher_id']);
            $academicY = Academic::find($validate['academic_id']);
            $academicYearAndSemester = $academicY->academicYear . ' ' . Helper::academicFormat($academicY->academicSemester);

            foreach ($request->input() as $key => $value) {
                if (strpos($key, 'criteria') === 0) {
                    $criteria = $value[0];
                    continue;
                }

                if (strpos($key, 'question') === 0) {
                    $question = $value[0];
                }

                if (strpos($key, 'answer') === 0) {
                    $answer = $value[0];

                    EvaluationList::create([
                        'restriction_id' => $validate['restriction_id'],
                        'academic_id' => $validate['academic_id'],
                        'teacher_id' => $validate['teacher_id'],
                        'course_id' => $validate['course_id'],
                        'subject_id' => $validate['subject_id'],
                        'teacher' => $validate['teacher'],
                        'criteria' => $criteria,
                        'question' => $question,
                        'answer' => $answer,
                    ]);
                }
            }

            $isStoreTeacherEvaluationStatus = TeacherEvaluationStatus::create([
                'restriction_id' => $validate['restriction_id'],
                'evaluator_id' => Auth::id(),
                'academic_id' => $validate['academic_id'],
                'teacher_id' => $validate['teacher_id'],
                'course_id' => $validate['course_id'],
                'subject_id' => $validate['subject_id'],
                'academicYear' => $academicY->academicYear,
                'academicYearAndSemester' => $academicYearAndSemester,
                'teacher' => $validate['teacher'],
                'teacherAvatar' => $teacher->teachersAvatar,
                'teacherEmail' => $teacher->teachersEmail,
                'course' => $course->courseName . '-' . $course->courseYearLevel . '' . $course->courseSection,
                'subjectCode' => $subject->subjectCode,
                'subjectName' => $subject->subjectName,
                'subjectDescription' => $subject->subjectDescription
            ]);
            if (!$isStoreTeacherEvaluationStatus) {
                return redirect()->back()->with('error', 'An error occurred while storing the data.');
            }
            return redirect()->route('teacher.evaluation')->with('success', $validate['teacher'] . ' has been successfully evaluated.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'An error occurred while storing the data.');
        }
    }
}

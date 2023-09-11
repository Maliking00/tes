<?php

namespace App\Http\Controllers;

use App\Models\EvaluationList;
use App\Models\TeacherEvaluationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdminAndHR']);
    }

    public function index()
    {
        $evaluatedTeacher = DB::table('teacher_evaluation_statuses')
            ->select([
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
                DB::raw('UNIX_TIMESTAMP(teacher_evaluation_statuses.created_at) as dateAssigned')
            ])
            ->join('academics', 'academics.id', '=', 'teacher_evaluation_statuses.academic_id')
            ->join('teachers', 'teachers.id', '=', 'teacher_evaluation_statuses.teacher_id')
            ->join('subjects', 'subjects.id', '=', 'teacher_evaluation_statuses.subject_id')
            ->join('courses', 'courses.id', '=', 'teacher_evaluation_statuses.course_id')
            ->orderBy('teacher_evaluation_statuses.updated_at', 'DESC')
            ->whereIn('teacher_evaluation_statuses.id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('teacher_evaluation_statuses')
                    ->groupBy('subject_id');
            })
            ->get()
            ->groupBy('academicYear');
        return view('reports.reports', compact(['evaluatedTeacher']));
    }

    public function showEvaluationResponses($academicID, $teacherID, $courseID, $subjectID)
    {
        $evalResponses = EvaluationList::where('academic_id', $academicID)
            ->where('teacher_id', $teacherID)
            ->where('course_id', $courseID)
            ->where('subject_id', $subjectID)
            ->get()
            ->groupBy('criteria');

        if ($evalResponses->count() === 0) {
            return back()->with('error', 'Opps! Attempting to retrieve data that does not exist.');
        }

        $restrictionID = EvaluationList::where('academic_id', $academicID)
            ->where('teacher_id', $teacherID)
            ->where('course_id', $courseID)
            ->where('subject_id', $subjectID)->first()->restriction_id;
        $data = DB::table('teacher_evaluation_statuses')->where('teacher_id', $teacherID)
            ->select([
                'teachers.teachersFullName as teacherName',
                'teachers.teachersAvatar as teacherAvatar',
                'courseName',
                'courseYearLevel',
                'courseSection',
                'subjectCode',
                'subjectName',
                'academicYear',
                'academicSemester',
                DB::raw('UNIX_TIMESTAMP(teacher_evaluation_statuses.created_at) as dateAssigned')
            ])
            ->join('academics', 'academics.id', '=', 'teacher_evaluation_statuses.academic_id')
            ->join('teachers', 'teachers.id', '=', 'teacher_evaluation_statuses.teacher_id')
            ->join('subjects', 'subjects.id', '=', 'teacher_evaluation_statuses.subject_id')
            ->join('courses', 'courses.id', '=', 'teacher_evaluation_statuses.course_id')
            ->where('academic_id', $academicID)
            ->where('course_id', $courseID)
            ->where('subject_id', $subjectID)
            ->where('restriction_id', $restrictionID)
            ->first();

        return view('reports.reports-response', compact(['data', 'evalResponses']));
    }
}

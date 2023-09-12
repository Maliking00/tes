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
        $evaluatedTeacher = TeacherEvaluationStatus::orderBy('created_at', 'DESC')->get()->groupBy('academicYearAndSemester')->map(function ($groupedItems) {
            return $groupedItems->unique('teacher_id');
        });;

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
        $data = TeacherEvaluationStatus::where('teacher_id', $teacherID)
            ->where('academic_id', $academicID)
            ->where('course_id', $courseID)
            ->where('subject_id', $subjectID)->first();

        return view('reports.reports-response', compact(['data', 'evalResponses']));
    }
}

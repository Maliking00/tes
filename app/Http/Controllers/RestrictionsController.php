<?php

namespace App\Http\Controllers;

use App\Models\Academic;
use App\Models\Courses;
use App\Models\Criterias;
use App\Models\Questionnaires;
use App\Models\Restriction;
use App\Models\Subjects;
use App\Models\Teachers;
use Illuminate\Http\Request;

class RestrictionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index($id, Academic $academicModel)
    {
        $academics = $academicModel->findOrFail($id);
        $models = [
            'questionnaires' => Questionnaires::where('academic_id', $academics->id)->get(),
            'teachers' => Teachers::get(),
            'courses' => Courses::get(),
            'subjects' => Subjects::get(),
        ];
        foreach ($models as $modelName => $model) {
            if ($model->isEmpty()) {
                return back()->with('warning', 'Please add ' . strtolower($modelName) . ' to assign restrictions. ' . (strtolower($modelName) == 'questionnaires' ? '' : '<a href="'.route(strtolower($modelName)).'" class="btn tes-btn btn-sm">Add ' . strtolower($modelName) . '</a>') . ' ');
            }
        }
        $criteriaList = Criterias::get();

        foreach ($criteriaList as $criteria) {
            if ($criteria->questionnaires->count() == 0) {
                return back()->with('warning', "Criteria {$criteria->criterias} has no questionnaires.\n");
            }
        }
        $restrictions = Restriction::where('academic_id', $academics->id)->get();
        return view('questionnaires.restrictions', compact(['academics', 'models', 'restrictions']));
    }

    public function storeRestriction(Request $request, Restriction $courseModel)
    {
        $validate = $request->validate([
            'academic_id' => 'required|string|exists:academics,id',
            'teacher_id' => 'required|string|exists:teachers,id',
            'course_id' => 'required|string|exists:courses,id',
            'subject_id' => 'required|string|exists:subjects,id'
        ]);

        $teacher = Teachers::find($validate['teacher_id']);
        $course = Courses::find($validate['course_id']);
        $subject = Subjects::find($validate['subject_id']);

        $isRestrictionCreated = $courseModel->create([
            'academic_id' => $validate['academic_id'],
            'teacher_id' => $validate['teacher_id'],
            'course_id' => $validate['course_id'],
            'subject_id' => $validate['subject_id'],
            'teacher' => $teacher->teachersFullName,
            'course' => $course->courseName,
            'subject' => $subject->subjectCode
        ]);

        if ($isRestrictionCreated) {
            return back()->with('success', 'Evaluation for ' . $teacher->teachersFullName . ' is ready.');
        }
        return back()->with('error', 'An error occurred.');
    }

    public function deleteRestriction($id, Restriction $restrictionModel)
    {
        $restriction = $restrictionModel->findOrFail($id);
        if (!$restriction->delete()) {
            return back()->with('error', 'An error occurred.');
        }

        return back()->with('success', 'Restriction successfully deleted.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Academic;
use App\Models\Courses;
use App\Models\Criterias;
use App\Models\EvaluationList;
use App\Models\Questionnaires;
use App\Models\Subjects;
use App\Models\Teachers;
use Illuminate\Http\Request;

class QuestionnairesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        $academics = Academic::all();
        if ($academics->count() > 0) {
            return view('questionnaires.questionnaires', compact('academics'));
        }
        return redirect()->route('academics')->with('warning', 'You cannot access questionnaire; add academic first.');
    }

    public function loadQuestionnaire(Request $request, Academic $academicModel)
    {
        $input = $request->search_input;
        $search = $academicModel->newQuery();
        $search->where(function ($query) use ($input) {
            $query->where('academicYear', 'like', "%{$input}%")->orWhere('academicSemester', 'like', "%{$input}%");
        });
        $academics = $search->orderBy('updated_at', 'DESC')->paginate(6);
        if ($academics->count() > 0) {
            $html = '<table class="table">
            <thead>
            <tr class="t-row-head" data-aos="fade-up" data-aos-delay="100">
                <th>Academic Year</th>
                <th>Semester</th>
                <th>Questions</th>
                <th>Answered</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>';

            $delay = 1;
            foreach ($academics as $academic) {
                $questions = Questionnaires::where('academic_id', $academic->id)->count();
                $answer = EvaluationList::where('academic_id', $academic->id)->count();
                $academicStatus = Academic::where('id', $academic->id)->first();
                $delay++;
                $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <td class="text-capitalize">' . $academic->academicYear . '</td>
                        <td class="text-capitalize">' . Helper::academicFormat($academic->academicSemester) . '</td>
                        <td class="text-lowercase">'.$questions.'</td>
                        <td class="text-lowercase">'.$answer.'</td>
                        <td class="text-lowercase">
                        <span class="badge text-capitalize '.($academicStatus->academicEvaluationStatus == 'Starting' ? 'bg-success' : 'bg-secondary').' text-light">'.$academicStatus->academicEvaluationStatus.'</span>
                        </td>
                        <td>
                        <div class="dropup">
                            <a class="badge bg-info text-light" href="' . route('show.manage.questionnaires', $academic->id) . '" title="Manage Questionnare">Manage</a>
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
                                <h3 class="font-weight-normal mt-4">No Academics found</h3>
                                <p>I\'m sorry, but the specified academic could not be found.</p>
                                <p>Please provide additional details or clarify your request for further assistance.</p>
                            </div>
                        </div>
                    </div>';
        }

        return response()->json([
            'table' => $html,
            'pagination' => $academics
        ], 200);
    }

    public function showManageQuestionnaire($id, Academic $academicModel){
        $academics = $academicModel->findOrFail($id);
        $criterias = Criterias::all();
        if ($criterias->count() > 0) {
            return view('questionnaires.manage-questionnaires', compact(['criterias', 'academics']));
        }
        return redirect()->route('criterias')->with('warning', 'You cannot add a question; add criteria first.');
    }

    public function loadManageQuestionnaire($id, Questionnaires $questionnaireModel, Criterias $criteriaModel)
    {
        $criterias = $criteriaModel->orderBy('created_at', 'DESC')->paginate(6);
        if ($criterias->count() > 0) {
            $html = '<div class="mb-4 shadow-sm bg-white p-3 rounded">
            <h3 class="mb-3">Rating Legend</h3>
            <div class="d-flex align-items-center justify-content-between">
            <small class="badge text-black bg-white shadow-sm">1 = Strongly Disagree</small>
            <small class="badge text-black bg-white shadow-sm">2 = Disagree</small>
            <small class="badge text-black bg-white shadow-sm">3 = Uncertain</small>
            <small class="badge text-black bg-white shadow-sm">4 = Agree</small>
            <small class="badge text-black bg-white shadow-sm">5 = Strongly Agree</small>
            </div>
            </div>';
            $delay = 1;
            $formNum = 2;
            foreach ($criterias as $criteria) {
                $html .= '<table class="table text-left questionnaires-table">
                <thead>';
                $delay++;
                $html .= '
                    <tr class="t-row-head"" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <th colspan="8" class="text-capitalize">' . $criteria->criterias . '</th>
                        <th class="text-lowercase">1</th>
                        <th class="text-lowercase">2</th>
                        <th class="text-lowercase">3</th>
                        <th class="text-lowercase">4</th>
                        <th class="text-lowercase">5</th>
                        <th></th>
                    </tr>
                </thead>
                    <tbody>';

                $delay2 = 2;
                $formNum2 = 1;
                $questionnaires = $questionnaireModel->where('criterias_id', $criteria->id)->where('academic_id', $id)->get();
                foreach ($questionnaires as $question) {
                    $delay2++;
                    $formNum++;
                    $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay2 . '00">
                        <td colspan="8" style="text-wrap: wrap;line-height: 20px;">' . $question->questions . '</td>
                        <td class="text-lowercase"><input type="radio" name="placeholder'.$formNum.'"></td>
                        <td class="text-lowercase"><input type="radio" name="placeholder'.$formNum.'"></td>
                        <td class="text-lowercase"><input type="radio" name="placeholder'.$formNum.'"></td>
                        <td class="text-lowercase"><input type="radio" name="placeholder'.$formNum.'"></td>
                        <td class="text-lowercase"><input type="radio" name="placeholder'.$formNum.'"></td>
                        <td>
                        <i class="ti-trash text-danger" onclick="document.querySelector(`#deleteQuestion'.$formNum . $formNum2 .'`).submit()"></i>
                        <form id="deleteQuestion'.$formNum . $formNum2 .'" method="POST" action="'.route('delete.questionnaire', $question->id).'"><input type="hidden" name="_token" value="'.csrf_token().'"></form>
                        </td>
                    </tr>';
                }
                $html .= '</tbody>
            </table>';
            }
        } else {
            $html = '<div class="v-100 text-center" data-aos="fade-up" data-aos-delay="400">
                        <div class="card">
                            <div class="card-body">
                                <img class="img-fluid" src="' . asset('/assets/images/404.jpg') . '" alt="Not found">
                                <h3 class="font-weight-normal mt-4">No Criteria found</h3>
                                <p>I\'m sorry, but the specified criteria could not be found.</p>
                                <p>Please provide additional details or clarify your request for further assistance.</p>
                            </div>
                        </div>
                    </div>';
        }

        return response()->json([
            'table' => $html,
            'pagination' => $criterias
        ], 200);
    }

    public function storeQuestionnaire(Request $request, Questionnaires $questionnaireModel)
    {
        $request->validate([
            'criterias' => 'required|string|exists:criterias,id',
            'academic_id' => 'required|string|exists:academics,id',
            'questions' => 'required|string'
        ]);

        $criteria = Criterias::where('id', $request->criterias)->first();

        $questionnaire = $questionnaireModel->create([
            'criterias_id' => $criteria->id,
            'academic_id' => $request->academic_id,
            'questions' => $request->questions
        ]);

        if ($questionnaire) {
            return response()->json([
                'success' => 'Questionnaire successfully added.',
            ], 200);
        }
    }

    public function updateAcademicEvaluationStatus($id, Request $request, Academic $academicModel)
    {
        $academic = $academicModel->findOrFail($id);
        $validatedData = $request->validate(
            [
                'academicEvaluationStatus' => 'in:Starting,Closed,Not started'
            ],
            [
                'academicEvaluationStatus.in' => 'Evaluation status is invalid.',
            ]
        );

        if (!$academic->update($validatedData)) {
            return back()->with('error', 'An error occurred.');
        }
        return back()->with('success', 'Evaluation status successfully updated.');
    }

    public function deleteQuestionnaire($id, Questionnaires $questionnaireModel)
    {
        $questionnaire = $questionnaireModel->findOrFail($id);
        if (!$questionnaire->delete()) {
            return back()->with('error', 'An error occurred.');
        }

        return back()->with('success', 'Question successfully deleted.');
    }
}

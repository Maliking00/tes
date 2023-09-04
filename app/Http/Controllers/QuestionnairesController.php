<?php

namespace App\Http\Controllers;

use App\Models\Criterias;
use App\Models\Questionnaires;
use Illuminate\Http\Request;

class QuestionnairesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        $criterias = Criterias::all();
        if ($criterias->count() > 0) {
            return view('questionnaires.questionnaires', compact('criterias'));
        }
        return redirect()->route('criterias')->with('warning', 'You cannot add a question; add criteria first.');
    }

    public function loadQuestionnaire(Request $request, Criterias $questionnaireModel)
    {
        $input = $request->search_input;
        $search = $questionnaireModel->newQuery();
        $search->where(function ($query) use ($input) {
            $query->where('criterias', 'like', "%{$input}%");
        });
        $criterias = $search->orderBy('created_at', 'DESC')->paginate(6);
        if ($criterias->count() > 0) {
            $html = '<div class="mb-4">
            <h3 class="mb-3" data-aos="fade-up" data-aos-delay="100">Rating Legend</h3>
            <div data-aos="fade-up" data-aos-delay="200" class="d-flex align-items-center justify-content-between">
            <small class="badge bg-danger">1 = Strongly Disagree</small>
            <small class="badge bg-secondary">2 = Disagree</small>
            <small class="badge bg-warning">3 = Uncertain</small>
            <small class="badge bg-info">4 = Agree</small>
            <small class="badge bg-primary">5 = Strongly Agree</small>
            </div>
            <hr data-aos="fade-up" data-aos-delay="300" class="my-4">
            </div>';
            $delay = 1;
            $formNum = 2;
            foreach ($criterias as $criteria) {
                $html .= '<table class="table text-left questionnaires-table">
                <thead>';
                $delay++;
                $html .= '
                    <tr class="t-row-head" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <th colspan="4" class="text-capitalize">' . $criteria->criterias . '</th>
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
                $questionnaires = Questionnaires::where('criteria_id', $criteria->id)->get();
                foreach ($questionnaires as $question) {
                    $delay2++;
                    $formNum++;
                    $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay2 . '00">
                        <td colspan="4">' . $question->questions . '</td>
                        <td class="text-lowercase"><input type="radio" name="placeholder"></td>
                        <td class="text-lowercase"><input type="radio" name="placeholder"></td>
                        <td class="text-lowercase"><input type="radio" name="placeholder"></td>
                        <td class="text-lowercase"><input type="radio" name="placeholder"></td>
                        <td class="text-lowercase"><input type="radio" name="placeholder"></td>
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
            'questions' => 'required|string'
        ]);

        $criteria = Criterias::where('id', $request->criterias)->first();

        $questionnaire = $questionnaireModel->create([
            'criteria_id' => $criteria->id,
            'questions' => $request->questions
        ]);

        if ($questionnaire) {
            return response()->json([
                'success' => 'Questionnaire successfully added.',
            ], 200);
        }
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

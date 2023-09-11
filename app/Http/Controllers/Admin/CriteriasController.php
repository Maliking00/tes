<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Criterias;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CriteriasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        return view('criterias.criterias');
    }

    public function loadCriterias(Request $request, Criterias $criteriaModel)
    {
        $input = $request->search_input;
        $search = $criteriaModel->newQuery();
        $search->where(function ($query) use ($input) {
            $query->where('criterias', 'like', "%{$input}%");
        });
        $criterias = $search->orderBy('updated_at', 'DESC')->paginate(6);
        if ($criterias->count() > 0) {
            $html = '<table class="table">
            <thead>
            <tr class="t-row-head" data-aos="fade-up" data-aos-delay="100">
                <th>Criteria</th>
                <th>Date Created</th>
                <th>Last Update</th>
                <th></th>
            </tr>
            </thead>
            <tbody>';

            $delay = 1;
            foreach ($criterias as $criteria) {
                $delay++;
                $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <td class="text-capitalize">' . $criteria->criterias . '</td>
                        <td class="text-lowercase">' . $criteria->created_at->diffForHumans() . '</td>
                        <td class="text-lowercase">' . $criteria->updated_at->diffForHumans() . '</td>
                        <td>
                        <div class="dropup">
                            <a href="' . route('show.edit.criteria', $criteria->id) . '"><i class="ti-angle-double-right show-options"></i></a>
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

    public function storeCriteria(Request $request, Criterias $criteriaModel)
    {
        try {
            $validatedData = $request->validate([
                'criterias' => 'required|string|unique:criterias',
            ]);
            if ($criteriaModel->create($validatedData)) {
                return response()->json([
                    'success' => $request->subjectName . ' successfully added.',
                ], 200);
            }
        } catch (ValidationException $e) {
            return new JsonResponse(['errors' => $e->errors()], 422);
        }
    }

    public function showEditCriteria($id, Criterias $criteriaModel)
    {
        $criteria = $criteriaModel->findOrFail($id);
        return view('criterias.edit-criterias', compact('criteria'));
    }

    public function updateCriteria($id, Request $request, Criterias $criteriaModel)
    {
        $criteria = $criteriaModel->findOrFail($id);
        $validatedData = $request->validate([
            'criterias' => 'required|string|unique:criterias,criterias,' . $criteria->id,
        ]);

        $existingCriteria = $criteriaModel->where('criterias', $validatedData['criterias'])->where('id', '<>', $id)->first();
        if ($existingCriteria) {
            $validatedData['criterias'] = $validatedData['criterias'] . '-copy';
        }

        if (!$criteria->update($validatedData)) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('criterias')->with('success', 'Criterias successfully updated.');
    }

    public function deleteCriteria($id, Criterias $criteriaModel)
    {
        $criteria = $criteriaModel->findOrFail($id);
        if (!$criteria->delete()) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('criterias')->with('success', 'Criteria successfully deleted.');
    }
}


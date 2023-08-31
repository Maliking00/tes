<?php

namespace App\Http\Controllers\AcademicFocus;

use App\Http\Controllers\Controller;
use App\Models\Academic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AcademicsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        return view('academicFocus.academics.academics');
    }

    public function loadAcademics(Request $request, Academic $academicModel)
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
                <th>Year</th>
                <th>Semester</th>
                <th>System Default</th>
                <th>Evaluation Status</th>
                <th>Last Update</th>
                <th></th>
            </tr>
            </thead>
            <tbody>';

            $delay = 1;
            foreach ($academics as $academic) {
                $delay++;
                $html .= '
                    <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                        <td class="text-capitalize">' . $academic->academicYear . '</td>
                        <td class="text-capitalize">' . $academic->academicSemester . '</td>
                        <td class="text-capitalize">' . $academic->academicSystemDefault . '</td>
                        <td class="text-capitalize">' . $academic->academicEvaluationStatus . '</td>
                        <td class="text-lowercase">' . $academic->updated_at->diffForHumans() . '</td>
                        <td>
                        <div class="dropup">
                            <a href="' . route('show.edit.academic', $academic->id) . '"><i class="ti-angle-double-right show-options"></i></a>
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
                                <img class="img-fluid" src="' . asset('/images/404.jpg') . '" alt="Not found">
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

    public function storeAcademic(Request $request, Academic $academicModel)
    {
        try {
            $validatedData = $request->validate([
                'academicYear' => 'required|string|unique:academics',
                'academicSemester' => 'required|string'
            ]);
            if ($academicModel->create($validatedData)) {
                return response()->json([
                    'success' => $request->courseName . ' successfully added.',
                ], 200);
            }
        } catch (ValidationException $e) {
            return new JsonResponse(['errors' => $e->errors()], 422);
        }
    }

    public function showEditAcademic($id, Academic $academicModel)
    {
        $academic = $academicModel->findOrFail($id);
        return view('academicFocus.academics.edit-academics', compact('academic'));
    }

    public function updateAcademic($id, Request $request, Academic $academicModel)
    {
        $academic = $academicModel->findOrFail($id);
        $validatedData = $request->validate([
            'academicYear' => 'required|string',
            'academicSemester' => 'required|string'
        ]);

        $existingAcademic = $academicModel->where('academicYear', $validatedData['academicYear'])->where('id', '<>', $id)->first();
        if ($existingAcademic) {
            $validatedData['academicYear'] = $validatedData['academicYear'] . '-copy';
        }

        if (!$academic->update($validatedData)) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('academics')->with('success', 'Academic successfully updated.');
    }

    public function deleteAcademic($id, Academic $academicModel)
    {
        $academics = $academicModel->findOrFail($id);
        if (!$academics->delete()) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('academics')->with('success', 'Academic successfully deleted.');
    }
}

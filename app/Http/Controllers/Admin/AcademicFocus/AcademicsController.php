<?php

namespace App\Http\Controllers\Admin\AcademicFocus;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Models\Academic;
use Illuminate\Http\Request;

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
                        <td class="text-capitalize">' . Helper::academicFormat($academic->academicSemester) . '</td>
                        <td class="text-capitalize">' . ($academic->academicSystemDefault ? '<span class="badge bg-primary">Yes</span>' : '<span class="badge bg-danger">No</span>') . '</td>
                        <td class="text-capitalize"><span class="badge bg-' . ($academic->academicEvaluationStatus == 'Starting' ? 'success' : ($academic->academicEvaluationStatus == 'Closed' ? 'secondary' : 'primary')) . '">' . $academic->academicEvaluationStatus . '</span></td>
                        <td class="text-lowercase">' . $academic->updated_at->diffForHumans() . '</td>
                        <td>
                        <div class="dropup">
                            <a href="' . route('show.edit.academic', $academic->id) . '" title="Edit"><i class="ti-angle-double-right show-options"></i></a>
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

    public function storeAcademic(Request $request, Academic $academicModel)
    {
        $validatedData = $request->validate(
            [
                'academicYear' => 'required|string|regex:/^\d{4}-\d{4}$/',
                'academicSemester' => 'required|numeric|in:1,2,3,4'
            ],
            [
                'academicSemester.in' => 'The semester must be only 1, 2, 3, or 4.',
            ]
        );

        $isAcademicYearAndSemesterExist = $academicModel::where('academicYear', $validatedData['academicYear'])->where('academicSemester', $validatedData['academicSemester'])->first();

        if ($isAcademicYearAndSemesterExist) {
            return response()->json([
                'errors' => array(
                    'academicYear' => array(
                        0 => 'Year is exist'
                    ),
                    'academicSemester' => array(
                        0 => 'Semester is exist'
                    ),
                )
            ], 422);
        }

        if ($academicModel->create($validatedData)) {
            return response()->json([
                'success' => $request->courseName . ' successfully added.',
            ], 200);
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
        $validatedData = $request->validate(
            [
                'academicYear' => 'required|string|regex:/^\d{4}-\d{4}$/',
                'academicSemester' => 'required|numeric|in:1,2,3,4'
            ],
            [
                'academicSemester.in' => 'The semester must be only 1, 2, 3, or 4.',
            ]
        );

        if (!$academic->update($validatedData)) {
            return back()->with('error', 'An error occurred.');
        }

        return redirect()->route('academics')->with('success', 'Academic successfully updated.');
    }

    public function updateAcademicDefaultYear($id, Request $request, Academic $academicModel)
    {
        $academic = $academicModel->findOrFail($id);
        $validatedData = $request->validate(
            [
                'academicSystemDefault' => 'in:0,1'
            ],
            [
                'academicSystemDefault.in' => 'System Default is invalid.',
            ]
        );

        $request->academicSystemDefault == 1 ? $validatedData['academicSystemDefault'] = 0 : $validatedData['academicSystemDefault'] = 1;

        if (!$academic->update($validatedData)) {
            return back()->with('error', 'An error occurred.');
        }
        return redirect()->route('academics')->with('success', 'System Default successfully updated.');
    }
    public function updateAcademicEvaluationStatus($id, Request $request, Academic $academicModel)
    {
        $academic = $academicModel->findOrFail($id);
        $validatedData = $request->validate([
            'academicEvaluationStatus' => 'in:Starting,Closed,Not started'
        ], [
            'academicEvaluationStatus.in' => 'Evaluation status is invalid.',
        ]);

        if ($validatedData['academicEvaluationStatus'] == 'Starting') {
            $academicModel->where('academicSystemDefault', 1)->update(['academicEvaluationStatus' => 'Not started', 'academicSystemDefault' => 0]);
            $academic->update(['academicEvaluationStatus' => 'Starting', 'academicSystemDefault' => 1]);
        }elseif($validatedData['academicEvaluationStatus'] == 'Closed'){
            $academic->update(['academicEvaluationStatus' => 'Closed']);
            $academic->update(['academicSystemDefault' => 0]);
        }else{
            $academic->where('academicSystemDefault', 1)->update(['academicEvaluationStatus' => 'Not started', 'academicSystemDefault' => 0]);
        }
        return redirect()->route('academics')->with('success', 'Evaluation status successfully updated.');
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

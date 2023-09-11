<?php

namespace App\Http\Controllers;

use App\Models\Academic;
use App\Models\Courses;
use App\Models\Teachers;
use App\Models\User;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $academics = Academic::where('academicSystemDefault', 1)->get();
        if ($academics->count() === 0) {
            $currentYear = Carbon::now()->year;
            $lastYear = $currentYear - 1;
            $formattedYears = $lastYear . '-' . $currentYear;

            $isNotCurrentYear = Academic::orderBy('academicSemester', 'DESC')->where('academicYear', $formattedYears)->first();
            if(!empty($isNotCurrentYear)){
                $academicDefault = $isNotCurrentYear;
            }else{
                $academicDefault = Academic::orderBy('academicSemester', 'DESC')->where('academicYear', '!=', $formattedYears)->first();
            }
        } else {
            $academicDefault = Academic::orderBy('academicSemester', 'DESC')->where('academicSystemDefault', 1)->first();
        }

        $models = [
            'usersOverAll' => User::count() + Teachers::count(),
            'students' => User::where('role', 'student')->count(),
            'pendingStudents' => User::where('role', 'student')->where('status', 'pending')->count(),
            'teachers' => Teachers::count(),
            'courses' => Courses::count(),
            'academicDefault' => $academicDefault
        ];

        return view('home', compact('models'));
    }
}

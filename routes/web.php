<?php

use App\Models\Subjects;
use Illuminate\Support\Facades\Route;

Auth::routes();
// force disable route 
Route::match(['get'], 'login', function () {
    return redirect('/');
})->name('login');

Route::get('/load-students-subjects/{id}', function ($id) {
    $subjectList = Subjects::where('course_id', $id)->get();
        $checkbox = '<label for="subjects" class="form-label">Select a subjects</label><br>
    <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
    ';
        foreach ($subjectList as $key => $subject) {
            $checkbox .= '<input type="checkbox" class="btn-check" name="subjects[]" id="sub' . $key . '" value="' . $subject->id . '">
        <label class="btn btn-outline-dark btn-sm mr-1" for="sub' . $key . '">' . $subject->subjectCode . '</label>';
        }
        $checkbox .= '</div><br><small class="text-danger" id="subjects-error"></small>';

    return response()->json([
        'checkbox' => $checkbox
    ], 200);
});

Route::get('/load-students-restriction-subjects/{id}', function ($id) {
    $subjectList = Subjects::where('course_id', $id)->get();
    $checkbox = '<label for="subject_id">Subjects</label><div>
        <select name="subject_id" id="subject_id" class="form-select form-control">
            <option selected>Choose</option>';

    foreach ($subjectList as $key => $subject) {
        $checkbox .= '<option value="' . $subject->id . '">' . $subject->subjectCode . '</option>';
    }

    $checkbox .= '</select></div>';

    return response()->json([
        'checkbox' => $checkbox
    ], 200);
});

// routes for guest only
Route::group(['middleware' => ['guest']], function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
    // registration
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register-data', [App\Http\Controllers\Auth\RegisterController::class, 'registrationFirst'])->name('register.data');
    Route::get('/registration-security-question', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationSecurityQuestion'])->name('register.security.question');
    Route::post('/registration-security-question', [App\Http\Controllers\Auth\RegisterController::class, 'postRegistrationSecurityQuestion']);
    Route::get('/registration-avatar-upload', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationAvatarUpload'])->name('register.avatar.upload');
    Route::post('/registration-avatar-upload', [App\Http\Controllers\Auth\RegisterController::class, 'postRegistrationAvatarUpload']);

    // login
    Route::post('/login-data', [App\Http\Controllers\Authentication::class, 'loginSecurityQuestion'])->name('login.data');
    Route::get('/login-security-question', [App\Http\Controllers\Authentication::class, 'showLoginSecurityQuestion'])->name('login.security.question');
    Route::post('/login-security-question', [App\Http\Controllers\Authentication::class, 'postLoginSecurityQuestion']);
    // OTP
    Route::get('/login-otp-security', [App\Http\Controllers\Authentication::class, 'showOtpPage'])->name('login.otp.security');
    Route::post('/verify-otp-security', [App\Http\Controllers\Authentication::class, 'verifyOtp'])->name('verify.otp.security');
    Route::post('/resend-otp-security', [App\Http\Controllers\Authentication::class, 'resendOTP'])->name('resend.otp.security');
});

// is Authenticate
Route::middleware('auth')->group(function () {
    Route::get('/for-approval', [App\Http\Controllers\ForApprovalController::class, 'index'])->name('show.for.approval');
    Route::middleware('isApproved')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

        // routes for admin only
        Route::middleware('isAdmin')->group(function () {
            // subjects
            Route::get('/dashboard/subjects', [App\Http\Controllers\Admin\AcademicFocus\SubjectsController::class, 'index'])->name('subjects');
            Route::get('/dashboard/load-subjects', [App\Http\Controllers\Admin\AcademicFocus\SubjectsController::class, 'loadSubjects'])->name('load.subjects');
            Route::post('/dashboard/store-subjects', [App\Http\Controllers\Admin\AcademicFocus\SubjectsController::class, 'storeSubject'])->name('store.subject');
            Route::get('/dashboard/subjects/{id}', [App\Http\Controllers\Admin\AcademicFocus\SubjectsController::class, 'showEditSubject'])->name('show.edit.subject');
            Route::post('/dashboard/subjects/update/{id}', [App\Http\Controllers\Admin\AcademicFocus\SubjectsController::class, 'updateSubject'])->name('update.subject');
            Route::post('/dashboard/subjects/delete/{id}', [App\Http\Controllers\Admin\AcademicFocus\SubjectsController::class, 'deleteSubject'])->name('delete.subject');

            // courses  
            Route::get('/dashboard/courses', [App\Http\Controllers\Admin\AcademicFocus\CoursesController::class, 'index'])->name('courses');
            Route::get('/dashboard/load-courses', [App\Http\Controllers\Admin\AcademicFocus\CoursesController::class, 'loadCourses'])->name('load.courses');
            Route::post('/dashboard/store-courses', [App\Http\Controllers\Admin\AcademicFocus\CoursesController::class, 'storeCourse'])->name('store.course');
            Route::get('/dashboard/courses/{id}', [App\Http\Controllers\Admin\AcademicFocus\CoursesController::class, 'showEditCourse'])->name('show.edit.course');
            Route::post('/dashboard/courses/update/{id}', [App\Http\Controllers\Admin\AcademicFocus\CoursesController::class, 'updateCourse'])->name('update.course');
            Route::post('/dashboard/courses/delete/{id}', [App\Http\Controllers\Admin\AcademicFocus\CoursesController::class, 'deleteCourse'])->name('delete.course');

            // academic year
            Route::get('/dashboard/academics', [App\Http\Controllers\Admin\AcademicFocus\AcademicsController::class, 'index'])->name('academics');
            Route::get('/dashboard/load-academics', [App\Http\Controllers\Admin\AcademicFocus\AcademicsController::class, 'loadAcademics'])->name('load.academics');
            Route::post('/dashboard/store-academics', [App\Http\Controllers\Admin\AcademicFocus\AcademicsController::class, 'storeAcademic'])->name('store.academic');
            Route::get('/dashboard/academics/{id}', [App\Http\Controllers\Admin\AcademicFocus\AcademicsController::class, 'showEditAcademic'])->name('show.edit.academic');
            Route::post('/dashboard/academics/update/{id}', [App\Http\Controllers\Admin\AcademicFocus\AcademicsController::class, 'updateAcademic'])->name('update.academic');
            Route::post('/dashboard/academics/update/academic/year/{id}', [App\Http\Controllers\Admin\AcademicFocus\AcademicsController::class, 'updateAcademicDefaultYear'])->name('update.academic.default.year');
            Route::post('/dashboard/academics/update/academic/evaluation-status/{id}', [App\Http\Controllers\Admin\AcademicFocus\AcademicsController::class, 'updateAcademicEvaluationStatus'])->name('update.academic.evaluation.status');
            Route::post('/dashboard/academics/delete/{id}', [App\Http\Controllers\Admin\AcademicFocus\AcademicsController::class, 'deleteAcademic'])->name('delete.academic');

            //teachers
            Route::get('/dashboard/teachers', [App\Http\Controllers\Admin\TeachersController::class, 'index'])->name('teachers');
            Route::get('/dashboard/load-teachers', [App\Http\Controllers\Admin\TeachersController::class, 'loadTeachers'])->name('load.teachers');
            Route::post('/dashboard/store-teachers', [App\Http\Controllers\Admin\TeachersController::class, 'storeTeacher'])->name('store.teacher');
            Route::get('/dashboard/teachers/{id}', [App\Http\Controllers\Admin\TeachersController::class, 'showEditTeacher'])->name('show.edit.teacher');
            Route::post('/dashboard/teachers/update/{id}', [App\Http\Controllers\Admin\TeachersController::class, 'updateTeacher'])->name('update.teacher');
            Route::post('/dashboard/teachers/delete/{id}', [App\Http\Controllers\Admin\TeachersController::class, 'deleteTeacher'])->name('delete.teacher');
            Route::post('/dashboard/teachers/update/avatar/{id}', [App\Http\Controllers\Admin\TeachersController::class, 'updateTeacherAvatar'])->name('update.teacher.avatar');

            // student
            Route::get('/dashboard/students', [App\Http\Controllers\Admin\StudentsController::class, 'index'])->name('students');
            Route::get('/dashboard/load-students', [App\Http\Controllers\Admin\StudentsController::class, 'loadStudents'])->name('load.students');
            Route::post('/dashboard/store-students', [App\Http\Controllers\Admin\StudentsController::class, 'storeStudent'])->name('store.student');
            Route::get('/dashboard/students/{id}', [App\Http\Controllers\Admin\StudentsController::class, 'showEditStudent'])->name('show.edit.student');
            Route::post('/dashboard/students/update/{id}', [App\Http\Controllers\Admin\StudentsController::class, 'updateStudent'])->name('update.student');
            Route::post('/dashboard/students/delete/{id}', [App\Http\Controllers\Admin\StudentsController::class, 'deleteStudent'])->name('delete.student');
            Route::post('/dashboard/students/update/role/{id}', [App\Http\Controllers\Admin\StudentsController::class, 'updateStudentRole'])->name('update.student.role');
            Route::post('/dashboard/students/update/status/{id}', [App\Http\Controllers\Admin\StudentsController::class, 'updateStudentStatus'])->name('update.student.status');
            Route::post('/dashboard/students/update/avatar/{id}', [App\Http\Controllers\Admin\StudentsController::class, 'updateStudentAvatar'])->name('update.student.avatar');

            // HR
            Route::get('/dashboard/hrs', [App\Http\Controllers\Admin\HrControllers::class, 'index'])->name('hrs');
            Route::get('/dashboard/load-hrs', [App\Http\Controllers\Admin\HrControllers::class, 'loadHrs'])->name('load.hrs');
            Route::post('/dashboard/store-hrs', [App\Http\Controllers\Admin\HrControllers::class, 'storeHr'])->name('store.hrs');
            Route::get('/dashboard/hrs/{id}', [App\Http\Controllers\Admin\HrControllers::class, 'showEditHr'])->name('show.edit.hr');
            Route::post('/dashboard/hrs/update/{id}', [App\Http\Controllers\Admin\HrControllers::class, 'updateHr'])->name('update.hr');
            Route::post('/dashboard/hrs/delete/{id}', [App\Http\Controllers\Admin\HrControllers::class, 'deleteHr'])->name('delete.hr');
            Route::post('/dashboard/hrs/update/role/{id}', [App\Http\Controllers\Admin\HrControllers::class, 'updateHrRole'])->name('update.hr.role');
            Route::post('/dashboard/hrs/update/status/{id}', [App\Http\Controllers\Admin\HrControllers::class, 'updateHrStatus'])->name('update.hr.status');
            Route::post('/dashboard/hrs/update/avatar/{id}', [App\Http\Controllers\Admin\HrControllers::class, 'updateHrAvatar'])->name('update.hr.avatar');

            // criteria
            Route::get('/dashboard/criterias', [App\Http\Controllers\Admin\CriteriasController::class, 'index'])->name('criterias');
            Route::get('/dashboard/load-criterias', [App\Http\Controllers\Admin\CriteriasController::class, 'loadCriterias'])->name('load.criterias');
            Route::post('/dashboard/store-criterias', [App\Http\Controllers\Admin\CriteriasController::class, 'storeCriteria'])->name('store.criteria');
            Route::get('/dashboard/criterias/{id}', [App\Http\Controllers\Admin\CriteriasController::class, 'showEditCriteria'])->name('show.edit.criteria');
            Route::post('/dashboard/criterias/update/{id}', [App\Http\Controllers\Admin\CriteriasController::class, 'updateCriteria'])->name('update.criteria');
            Route::post('/dashboard/criterias/delete/{id}', [App\Http\Controllers\Admin\CriteriasController::class, 'deleteCriteria'])->name('delete.criteria');

            // questionnaire
            Route::get('/dashboard/questionnaires', [App\Http\Controllers\Admin\QuestionnairesController::class, 'index'])->name('questionnaires');
            Route::get('/dashboard/load-questionnaires', [App\Http\Controllers\Admin\QuestionnairesController::class, 'loadQuestionnaire'])->name('load.questionnaires');
            Route::get('/dashboard/load-questionnaires-list', [App\Http\Controllers\Admin\QuestionnairesController::class, 'loadQuestionnaireList'])->name('questionnaireList');
            Route::get('/dashboard/questionnaires/{id}', [App\Http\Controllers\Admin\QuestionnairesController::class, 'showManageQuestionnaire'])->name('show.manage.questionnaires');
            Route::get('/dashboard/load-manage-questionnaire/{id}', [App\Http\Controllers\Admin\QuestionnairesController::class, 'loadManageQuestionnaire'])->name('load.manage.questionnaires');
            Route::post('/dashboard/store-questionnaires', [App\Http\Controllers\Admin\QuestionnairesController::class, 'storeQuestionnaire'])->name('store.questionnaire');
            Route::post('/dashboard/questionnaires/update-academic-status/{id}', [App\Http\Controllers\Admin\QuestionnairesController::class, 'updateAcademicEvaluationStatus'])->name('update.academic.status');
            Route::post('/dashboard/questionnaires/delete/{id}', [App\Http\Controllers\Admin\QuestionnairesController::class, 'deleteQuestionnaire'])->name('delete.questionnaire');

            // restrictions
            Route::get('/dashboard/questionnaires/{id}/restrictions', [App\Http\Controllers\Admin\RestrictionsController::class, 'index'])->name('restrictions');
            Route::post('/dashboard/store-restrictions', [App\Http\Controllers\Admin\RestrictionsController::class, 'storeRestriction'])->name('store.restriction');
            Route::post('/dashboard/restrictions/delete/{id}', [App\Http\Controllers\Admin\RestrictionsController::class, 'deleteRestriction'])->name('delete.restriction');

            // settings
            Route::get('/dashboard/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
            Route::post('/dashboard/settings', [App\Http\Controllers\Admin\SettingsController::class, 'updateSetting']);
            Route::post('/dashboard/settings/update/{id}', [App\Http\Controllers\Admin\SettingsController::class, 'updateAdmin'])->name('update.admin.credentials');
        });

        // Evaluation Report
        Route::get('/dashboard/evaluation-reports', [App\Http\Controllers\EvaluationReportsController::class, 'index'])->name('evaluation.reports');
        Route::get('/dashboard/evaluation-reports/{academicID}/{teacherID}/{courseID}/{subjectID}', [App\Http\Controllers\EvaluationReportsController::class, 'showEvaluationResponses'])->name('evaluation.reports.responses');
        // Route::get('/dashboard/evaluation-reports/{academicID}/{teacherID}/{courseID}/{subjectID}/{restrictionID}', [App\Http\Controllers\EvaluationReportsController::class, 'showEvaluationResponses'])->name('evaluation.reports.responses');

        // Student routes
        Route::get('/dashboard/teacher-evaluation', [App\Http\Controllers\EvaluationsController::class, 'index'])->name('teacher.evaluation');
        Route::get('/dashboard/teacher-evaluation/{id}/{course}/{teacherID}/{subject_id}', [App\Http\Controllers\EvaluationsController::class, 'evaluateSpecificTeacher'])->name('teacher.evaluation.academic');
        Route::post('/dashboard/teacher-evaluation/store', [App\Http\Controllers\EvaluationsController::class, 'storeEvaluateSpecificTeacher'])->name('teacher.evaluation.academic.store');
    });
});

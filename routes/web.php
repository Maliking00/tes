<?php

use App\Helper\Helper;
use Illuminate\Support\Facades\Route;

Auth::routes();
// force disable route 
Route::match(['get'], 'login', function () {
    return redirect('/');
})->name('login');

// routes for guest only
Route::group(['middleware' => ['guest']], function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    // registration
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
    // Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::middleware('isApproved')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

        // routes for admin only
        Route::middleware('isAdmin')->group(function () {
            // subjects
            Route::get('/dashboard/subjects', [App\Http\Controllers\AcademicFocus\SubjectsController::class, 'index'])->name('subjects');
            Route::get('/dashboard/load-subjects', [App\Http\Controllers\AcademicFocus\SubjectsController::class, 'loadSubjects'])->name('load.subjects');
            Route::post('/dashboard/store-subjects', [App\Http\Controllers\AcademicFocus\SubjectsController::class, 'storeSubject'])->name('store.subject');
            Route::get('/dashboard/subjects/{id}', [App\Http\Controllers\AcademicFocus\SubjectsController::class, 'showEditSubject'])->name('show.edit.subject');
            Route::post('/dashboard/subjects/update/{id}', [App\Http\Controllers\AcademicFocus\SubjectsController::class, 'updateSubject'])->name('update.subject');
            Route::post('/dashboard/subjects/delete/{id}', [App\Http\Controllers\AcademicFocus\SubjectsController::class, 'deleteSubject'])->name('delete.subject');

            // courses 711085 
            Route::get('/dashboard/courses', [App\Http\Controllers\AcademicFocus\CoursesController::class, 'index'])->name('courses');
            Route::get('/dashboard/load-courses', [App\Http\Controllers\AcademicFocus\CoursesController::class, 'loadCourses'])->name('load.courses');
            Route::post('/dashboard/store-courses', [App\Http\Controllers\AcademicFocus\CoursesController::class, 'storeCourse'])->name('store.course');
            Route::get('/dashboard/courses/{id}', [App\Http\Controllers\AcademicFocus\CoursesController::class, 'showEditCourse'])->name('show.edit.course');
            Route::post('/dashboard/courses/update/{id}', [App\Http\Controllers\AcademicFocus\CoursesController::class, 'updateCourse'])->name('update.course');
            Route::post('/dashboard/courses/delete/{id}', [App\Http\Controllers\AcademicFocus\CoursesController::class, 'deleteCourse'])->name('delete.course');

            // academic year
            Route::get('/dashboard/academics', [App\Http\Controllers\AcademicFocus\AcademicsController::class, 'index'])->name('academics');
            Route::get('/dashboard/load-academics', [App\Http\Controllers\AcademicFocus\AcademicsController::class, 'loadAcademics'])->name('load.academics');
            Route::post('/dashboard/store-academics', [App\Http\Controllers\AcademicFocus\AcademicsController::class, 'storeAcademic'])->name('store.academic');
            Route::get('/dashboard/academics/{id}', [App\Http\Controllers\AcademicFocus\AcademicsController::class, 'showEditAcademic'])->name('show.edit.academic');
            Route::post('/dashboard/academics/update/{id}', [App\Http\Controllers\AcademicFocus\AcademicsController::class, 'updateAcademic'])->name('update.academic');
            Route::post('/dashboard/academics/update/academic/year/{id}', [App\Http\Controllers\AcademicFocus\AcademicsController::class, 'updateAcademicDefaultYear'])->name('update.academic.default.year');
            Route::post('/dashboard/academics/update/academic/evaluation-status/{id}', [App\Http\Controllers\AcademicFocus\AcademicsController::class, 'updateAcademicEvaluationStatus'])->name('update.academic.evaluation.status');
            Route::post('/dashboard/academics/delete/{id}', [App\Http\Controllers\AcademicFocus\AcademicsController::class, 'deleteAcademic'])->name('delete.academic');

            //teachers
            Route::get('/dashboard/teachers', [App\Http\Controllers\TeachersController::class, 'index'])->name('teachers');
            Route::get('/dashboard/load-teachers', [App\Http\Controllers\TeachersController::class, 'loadTeachers'])->name('load.teachers');
            Route::post('/dashboard/store-teachers', [App\Http\Controllers\TeachersController::class, 'storeTeacher'])->name('store.teacher');
            Route::get('/dashboard/teachers/{id}', [App\Http\Controllers\TeachersController::class, 'showEditTeacher'])->name('show.edit.teacher');
            Route::post('/dashboard/teachers/update/{id}', [App\Http\Controllers\TeachersController::class, 'updateTeacher'])->name('update.teacher');
            Route::post('/dashboard/teachers/delete/{id}', [App\Http\Controllers\TeachersController::class, 'deleteTeacher'])->name('delete.teacher');
            Route::post('/dashboard/teachers/update/avatar/{id}', [App\Http\Controllers\TeachersController::class, 'updateTeacherAvatar'])->name('update.teacher.avatar');

            // student
            Route::get('/dashboard/students', [App\Http\Controllers\StudentsController::class, 'index'])->name('students');
            Route::get('/dashboard/load-students', [App\Http\Controllers\StudentsController::class, 'loadStudents'])->name('load.students');
            Route::post('/dashboard/store-students', [App\Http\Controllers\StudentsController::class, 'storeStudent'])->name('store.student');
            Route::get('/dashboard/students/{id}', [App\Http\Controllers\StudentsController::class, 'showEditStudent'])->name('show.edit.student');
            Route::post('/dashboard/students/update/{id}', [App\Http\Controllers\StudentsController::class, 'updateStudent'])->name('update.student');
            Route::post('/dashboard/students/delete/{id}', [App\Http\Controllers\StudentsController::class, 'deleteStudent'])->name('delete.student');
            Route::post('/dashboard/students/update/role/{id}', [App\Http\Controllers\StudentsController::class, 'updateStudentRole'])->name('update.student.role');
            Route::post('/dashboard/students/update/status/{id}', [App\Http\Controllers\StudentsController::class, 'updateStudentStatus'])->name('update.student.status');
            Route::post('/dashboard/students/update/avatar/{id}', [App\Http\Controllers\StudentsController::class, 'updateStudentAvatar'])->name('update.student.avatar');

            // HR
            Route::get('/dashboard/hrs', [App\Http\Controllers\HrControllers::class, 'index'])->name('hrs');
            Route::get('/dashboard/load-hrs', [App\Http\Controllers\HrControllers::class, 'loadHrs'])->name('load.hrs');
            Route::post('/dashboard/store-hrs', [App\Http\Controllers\HrControllers::class, 'storeHr'])->name('store.hrs');
            Route::get('/dashboard/hrs/{id}', [App\Http\Controllers\HrControllers::class, 'showEditHr'])->name('show.edit.hr');
            Route::post('/dashboard/hrs/update/{id}', [App\Http\Controllers\HrControllers::class, 'updateHr'])->name('update.hr');
            Route::post('/dashboard/hrs/delete/{id}', [App\Http\Controllers\HrControllers::class, 'deleteHr'])->name('delete.hr');
            Route::post('/dashboard/hrs/update/role/{id}', [App\Http\Controllers\HrControllers::class, 'updateHrRole'])->name('update.hr.role');
            Route::post('/dashboard/hrs/update/status/{id}', [App\Http\Controllers\HrControllers::class, 'updateHrStatus'])->name('update.hr.status');
            Route::post('/dashboard/hrs/update/avatar/{id}', [App\Http\Controllers\HrControllers::class, 'updateHrAvatar'])->name('update.hr.avatar');

            // criteria
            Route::get('/dashboard/criterias', [App\Http\Controllers\CriteriasController::class, 'index'])->name('criterias');
            Route::get('/dashboard/load-criterias', [App\Http\Controllers\CriteriasController::class, 'loadCriterias'])->name('load.criterias');
            Route::post('/dashboard/store-criterias', [App\Http\Controllers\CriteriasController::class, 'storeCriteria'])->name('store.criteria');
            Route::get('/dashboard/criterias/{id}', [App\Http\Controllers\CriteriasController::class, 'showEditCriteria'])->name('show.edit.criteria');
            Route::post('/dashboard/criterias/update/{id}', [App\Http\Controllers\CriteriasController::class, 'updateCriteria'])->name('update.criteria');
            Route::post('/dashboard/criterias/delete/{id}', [App\Http\Controllers\CriteriasController::class, 'deleteCriteria'])->name('delete.criteria');

            // questionnaire
            Route::get('/dashboard/questionnaires', [App\Http\Controllers\QuestionnairesController::class, 'index'])->name('questionnaires');
            Route::get('/dashboard/load-questionnaires', [App\Http\Controllers\QuestionnairesController::class, 'loadQuestionnaire'])->name('load.questionnaires');
            Route::post('/dashboard/store-questionnaires', [App\Http\Controllers\QuestionnairesController::class, 'storeQuestionnaire'])->name('store.questionnaire');
            Route::post('/dashboard/questionnaires/delete/{id}', [App\Http\Controllers\QuestionnairesController::class, 'deleteQuestionnaire'])->name('delete.questionnaire');
        });

        // // routes for HR only
        // Route::middleware('isHR')->group(function () {
        // });

        // // routes for Students only
        // Route::middleware('isStudent')->group(function () {
        // });
    });
});

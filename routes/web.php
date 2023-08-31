<?php

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

    // login
    Route::post('/login-data', [App\Http\Controllers\Authentication::class, 'loginSecurityQuestion'])->name('login.data');
    Route::get('/login-security-question', [App\Http\Controllers\Authentication::class, 'showLoginSecurityQuestion'])->name('login.security.question');
    Route::post('/login-security-question', [App\Http\Controllers\Authentication::class, 'postLoginSecurityQuestion']);
    // OTP
    Route::get('/login-otp-security', [App\Http\Controllers\Authentication::class, 'showOtpPage'])->name('login.otp.security');
    Route::post('/verify-otp-security', [App\Http\Controllers\Authentication::class, 'verifyOtp'])->name('verify.otp.security');
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

            // courses
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
            Route::post('/dashboard/academics/delete/{id}', [App\Http\Controllers\AcademicFocus\AcademicsController::class, 'deleteAcademic'])->name('delete.academic');
        });

        // // routes for HR only
        // Route::middleware('isHR')->group(function () {
        // });

        // // routes for Students only
        // Route::middleware('isStudent')->group(function () {
        // });
    });
});

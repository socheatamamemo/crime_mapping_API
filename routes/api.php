<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CrimeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});
Route::get('users',[RegisteredUserController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::get('/crime-reports', [CrimeController::class, 'displayCrimereport'])->name('user.displayCrimereport');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('verify-email', [EmailVerificationPromptController::class, 'show'])->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])->name('password.confirm');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
});

//ContactUs
Route::middleware('auth:sanctum')->get('/contact-us-auth', [ContactController::class, 'createAuth'])->name('api.contactUsAuth');
Route::middleware('auth:sanctum')->post('/contact-us-auth', [ContactController::class, 'storeAuth'])->name('api.contact.authSubmit');

//report User
Route::middleware('auth:sanctum')->get('/crime-reports', [CrimeController::class, 'displayCrimereport']);
Route::middleware('auth:sanctum')->post('/report-crime', [CrimeController::class, 'reportCrime']);

//profile User
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/userprofile', [ProfileController::class, 'show'])->name('userprofile.show');
    Route::get('/userprofile/edit', [ProfileController::class, 'edit'])->name('userprofile.edit');
    Route::put('/userprofile', [ProfileController::class, 'update'])->name('userprofile.update');
    Route::delete('/userprofile', [ProfileController::class, 'destroy'])->name('userprofile.destroy');
});


//Admin Register
Route::post('/admin_register', [AdminController::class, 'adminRegister']);
Route::post('/admin_login', [AdminController::class, 'adminLogin']);
Route::post('/admin_logout', [AdminController::class, 'adminLogout'])->middleware('auth:sanctum');
Route::get('/user_dash', [DashboardController::class, 'displayUser'])->name('displayUser');



//Display Pending Crime Admin
Route::middleware('auth:sanctum')->get('/pending_report', [DashboardController::class, 'displayPendingCrime']);

//Display Crime Admin
Route::get('/crimes', [DashboardController::class, 'displayCrime']);
//admin profile
Route::middleware('auth:sanctum')->get('/admin_profile', function () {
    $admin = auth()->user();
    return response()->json(['admin' => $admin]);
});

//Admin Crime Type
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/crimetype', [DashboardController::class, 'storeCrimeType']);
    Route::get('/showcrimetype', [DashboardController::class, 'showCrimeType'])->name('displayCrimeType');
});

 // Delete, Edit, and Confirm Function
 Route::delete('/deleteCrimetype/{id}', [DashboardController::class, 'deleteCrimetype'])->name('deleteCrimetype.api');
 Route::delete('/delete_user/{user}', [UserController::class, 'deleteUser'])->name('deleteUser.api');
 Route::delete('/delete_crime/{crime}', [CrimeController::class, 'deleteCrime'])->name('deleteCrime.api');

 Route::put('/crimes/{crime}', [CrimeController::class, 'update'])->name('crimes.update');
 Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');


 Route::post('/confirm_pending_crime/{crime_pending}', [CrimeController::class, 'approvePendingCrime'])->name('approvePendingCrime');
 Route::delete('/delete_pending_crime/{crime_pending}', [CrimeController::class, 'deletePendingCrime'])->name('deletePendingCrime');




 Route::middleware('auth:sanctum')->group(function () {
    Route::get('/contacts', [DashboardController::class, 'displayContacts'])->name('api.displayContacts');
    Route::delete('/contacts/{contact}', [ContactController::class, 'deleteContact'])->name('api.deleteContact');
});
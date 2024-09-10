<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ContactPersonController;
use App\Http\Controllers\IzinAdminController;
use App\Http\Controllers\SakitAdminController;
use App\Http\Controllers\SakitController;
use App\Http\Controllers\WhyTradersChooseUsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::post('/token/check', [TokenController::class, 'check']);


Route::middleware(['web', 'disableBackButton'])->group(function(){
    Route::middleware(['disableRedirectToLoginPage'])->group(function(){
        Route::get('/', [AuthenticationController::class, 'login'])->name('index');
        Route::get('login', [AuthenticationController::class, 'login'])->name('login');
        Route::post('login', [AuthenticationController::class, 'postLogin'])->name('post.login');
    });
    
    Route::get('logout', [AuthenticationController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')->group(function(){
    Route::middleware(['auth:web', 'disableBackButton', 'admin'])->group(function(){
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('experience', ExperienceController::class);
        Route::resource('admin', AdminController::class);
        Route::resource('user', UserAdminController::class);
        Route::resource('lokasi', LokasiController::class);
        Route::get('/lokasi/preview/{slug}', [LokasiController::class, 'preview'])->name('lokasi.preview');
        Route::resource('lokasi', LokasiController::class);
        Route::get('/token', [TokenController::class, 'token'])->name('token');
        Route::get('/absen', [AbsenController::class, 'absen'])->name('absen');
        Route::get('/izin', [IzinAdminController::class, 'izin'])->name('izin');
        Route::get('/izin/{kode}', [IzinAdminController::class, 'show'])->name('izin.show');
        Route::put('/izin/approve/{id}', [IzinAdminController::class, 'approve'])->name('izin.approve');
        Route::put('/izin/reject/{id}', [IzinAdminController::class, 'reject'])->name('izin.reject');
        Route::get('/sakit', [SakitAdminController::class, 'sakit'])->name('sakit');
        Route::get('/sakit/{kode}', [SakitAdminController::class, 'show'])->name('sakit.show');
        Route::put('/sakit/approve/{id}', [SakitAdminController::class, 'approve'])->name('sakit.approve');
        Route::put('/sakit/reject/{id}', [SakitAdminController::class, 'reject'])->name('sakit.reject');
        Route::get('/contact-person', [ContactPersonController::class, 'index'])->name('contact-person.index');
        Route::put('/contact-person/{id}', [ContactPersonController::class, 'update'])->name('contact-person.update');
    });
});

Route::prefix('user')->name('user.')->group(function(){
    Route::middleware(['auth:web', 'disableBackButton', 'user'])->group(function(){
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('/shift', [UserController::class, 'shift'])->name('shift');
        Route::get('/index', [UserController::class, 'index'])->name('index');
        Route::get('/response/{kode}', [UserController::class, 'response'])->name('response');
        Route::post('/absen', [UserController::class, 'absen'])->name('absen');
        Route::get('/history', [UserController::class, 'history'])->name('history');
        Route::resource('izin', IzinController::class);
        Route::resource('sakit', SakitController::class);
    });
});

<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\HttpController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, 'index']);
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/register', [HomeController::class, 'register'])->name('register');



Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');


Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


//Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth');


Route::group(
    [
        'middleware' => ['role:teacher', 'auth']
    ],
    function () {
        Route::get('/teacher', [HomeController::class, 'teacher_home']);
    }
);

Route::group(
    [
        'middleware' => ['role:student', 'auth']
    ],
    function () {
        Route::get('/student', [HomeController::class, 'student_home']);
    }
);

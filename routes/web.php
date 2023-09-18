<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::post('/fetch_data', [HomeController::class, 'fetch_data'])->middleware('auth');
Route::get('/get_websites', [HomeController::class, 'get_websites'])->middleware('auth');
Route::post('/bot_check', [HomeController::class, 'bot_check'])->middleware('auth');
Route::post('/add_domain', [HomeController::class, 'add_domain'])->middleware('auth');
Route::post('/ubah_domain', [HomeController::class, 'ubah_domain'])->middleware('auth');
Route::post('/delete_domain', [HomeController::class, 'delete_domain'])->middleware('auth');

Route::get('/profile', [ProfileController::class, 'index'])->middleware('auth');
Route::get('/get_package', [ProfileController::class, 'get_package'])->middleware('auth');
Route::post('/ubah_nama_bo', [ProfileController::class, 'ubah_nama_bo'])->middleware('auth');
Route::post('/cek_password_bo', [AuthController::class, 'cek_password_bo'])->middleware('auth');
Route::post('/ubah_password_bo', [ProfileController::class, 'ubah_password_bo'])->middleware('auth');

Route::get('/get_session', [AuthController::class, 'get_session'])->middleware('auth');

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/validateLogin', [AuthController::class, 'validateLogin']);

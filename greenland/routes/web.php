<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// use TCG\Voyager\Voyager;
use TCG\Voyager\Facades\Voyager;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Controller::class, 'home']);
Route::get('/about', function () {
    return view('about');
});
Route::get('/contact', function () {
    return view('contact');
});
Route::get('/services', [Controller::class,'viewServices']);


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//edit user data
Route::put('/edituser/{id}', [App\Http\Controllers\Controller::class, 'edituser']);

//call service for users
Route::get('/volunteer/{service_id}', [App\Http\Controllers\Controller::class, 'viewvolunteer'])->middleware('auth');
Route::get('/volunteer/{service_id}/user/{user_id}', [App\Http\Controllers\Controller::class, 'volunteer'])->middleware('auth');

//add image in registration
Route::post('/store-image',[App\Http\Controllers\Controller::class,'storeImage'])
->name('images.store');


// sign for newsletter
Route::post('/news', [App\Http\Controllers\Controller::class, 'news']);

// contact us
Route::post('/contact', [App\Http\Controllers\Controller::class, 'contact']);


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::get('/admin/approve',[Controller::class, 'render']);
Route::get('/admin/dashboard',[Controller::class, 'dashboard']);
// Route::get('/admin',[Controller::class, 'dashboard']);
Route::post('/admin/approve/done',[Controller::class, 'aprrove']);

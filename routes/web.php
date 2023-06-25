<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Admin\GBGCmsController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('user')->name('user.')->group(function(){
    Route::middleware(['guest:web'])->group(function(){
        Route::view('/login', 'user.login')->name('login');
        Route::view('/register', 'user.register')->name('register');
        Route::any('/create-user', [UserController::class, 'create_user'])->name('create-user');
        Route::any('/dologin',[UserController::class, 'dologin'])->name('dologin');
    });
    Route::middleware(['auth:web'])->group(function(){
        Route::view('/home', 'user.home')->name('home');
        Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    });
});

Route::prefix('admin')->name('admin.')->group(function(){

    Route::middleware(['guest:admin'])->group(function(){
        Route::view('/login', 'admin.login')->name('login');
        Route::any('/dologin',[AdminController::class, 'dologin'])->name('dologin');
    });

    Route::middleware(['auth:admin'])->group(function(){
        Route::view('/home', 'admin.home')->name('home');
        //Route::any('/home', [AdminController::class, 'home'])->name('home');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        
        Route::group(['prefix' => 'gbg', 'as' => 'gbg.'], function () {
            
            Route::group(['prefix' => 'cms', 'as' => 'cms.'], function () {
                Route::any('/', [GBGCmsController::class, 'list'])->name('list');
                Route::any('/add', [GBGCmsController::class, 'add'])->name('add');
                Route::any('/edit/{id}', [GBGCmsController::class, 'edit'])->name('edit');
                Route::get('/delete/{id}', [GBGCmsController::class, 'delete'])->name('delete');
                Route::post('/status', [GBGCmsController::class, 'status'])->name('status');
            });

        });
    });
});

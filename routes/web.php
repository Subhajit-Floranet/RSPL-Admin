<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Admin\GBGCmsController;
use App\Http\Controllers\Admin\GBGCategoryController;
use App\Http\Controllers\Admin\GBGFalseUrlController;
use App\Http\Controllers\Admin\GBGProductController;
use App\Http\Controllers\Admin\GBGCouponController;
use App\Http\Controllers\Admin\GBGHomeFeatureManagementController;
use App\Http\Controllers\Admin\GBGTestimonialManagementController;
use App\Http\Controllers\Admin\GBGAddonController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\ContactManagementController;

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

            Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
                Route::any('/', [GBGCategoryController::class, 'list'])->name('list');
                Route::any('/add', [GBGCategoryController::class, 'add'])->name('add');
                Route::any('/edit/{id}', [GBGCategoryController::class, 'edit'])->name('edit');
                Route::get('/delete/{id}', [GBGCategoryController::class, 'delete'])->name('delete');
                Route::post('/status', [GBGCategoryController::class, 'status'])->name('status');
                Route::get('/deleteimage/{id}', [GBGCategoryController::class, 'deleteimage'])->name('deleteimage');
            });

            Route::group(['prefix' => 'falseurl', 'as' => 'falseurl.'], function () {
                Route::any('/', [GBGFalseUrlController::class, 'list'])->name('list');
                Route::any('/add', [GBGFalseUrlController::class, 'add'])->name('add');
                Route::any('/edit/{id}', [GBGFalseUrlController::class, 'edit'])->name('edit');
                Route::get('/delete/{id}', [GBGFalseUrlController::class, 'delete'])->name('delete');
                Route::post('/status', [GBGFalseUrlController::class, 'status'])->name('status');
                Route::get('/deleteimage/{id}', [GBGFalseUrlController::class, 'deleteimage'])->name('deleteimage');
            });

            Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
                Route::any('/', [GBGProductController::class, 'list'])->name('list');
                Route::any('/add', [GBGProductController::class, 'add'])->name('add');
                Route::any('/edit/{id}', [GBGProductController::class, 'edit'])->name('edit');
                Route::get('/delete/{id}', [GBGProductController::class, 'delete'])->name('delete');
                Route::post('/status', [GBGProductController::class, 'status'])->name('status');
                Route::get('/deleteimage/{id}', [GBGProductController::class, 'deleteimage'])->name('deleteimage');
                Route::any('/deleteattribute', [GBGProductController::class, 'deleteattribute'])->name('deleteattribute');
            });
            
            Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
                Route::any('/', [GBGCouponController::class, 'list'])->name('list');
                Route::any('/add', [GBGCouponController::class, 'add'])->name('add');
                Route::any('/edit/{id}', [GBGCouponController::class, 'edit'])->name('edit');
                Route::get('/delete/{id}', [GBGCouponController::class, 'delete'])->name('delete');
                Route::post('/status', [GBGCouponController::class, 'status'])->name('status');
                Route::get('/deleteimage/{id}', [GBGCouponController::class, 'deleteimage'])->name('deleteimage');
               Route::any('/deleteattribute', [GBGCouponController::class, 'deleteattribute'])->name('deleteattribute');
            });

            Route::group(['prefix' => 'homefeaturemanagement', 'as' => 'homefeaturemanagement.'], function () {
                Route::any('/', [GBGHomeFeatureManagementController::class, 'list'])->name('list');
                Route::any('/add', [GBGHomeFeatureManagementController::class, 'add'])->name('add');
                Route::any('/edit/{id}', [GBGHomeFeatureManagementController::class, 'edit'])->name('edit');
                Route::get('/delete/{id}', [GBGHomeFeatureManagementController::class, 'delete'])->name('delete');
                Route::post('/status', [GBGHomeFeatureManagementController::class, 'status'])->name('status');
                Route::get('/deleteimage/{id}', [GBGHomeFeatureManagementController::class, 'deleteimage'])->name('deleteimage');
                Route::any('/deleteattribute', [GBGHomeFeatureManagementController::class, 'deleteattribute'])->name('deleteattribute');
                Route::any('/categoryproduct', [GBGHomeFeatureManagementController::class, 'categoryproduct'])->name('categoryproduct');
            });

            Route::group(['prefix' => 'testimonialmanagement', 'as' => 'testimonialmanagement.'], function () {
                Route::any('/', [GBGTestimonialManagementController::class, 'list'])->name('list');
                Route::any('/add', [GBGTestimonialManagementController::class, 'add'])->name('add');
                Route::any('/edit/{id}', [GBGTestimonialManagementController::class, 'edit'])->name('edit');
                Route::get('/delete/{id}', [GBGTestimonialManagementController::class, 'delete'])->name('delete');
                Route::post('/status', [GBGTestimonialManagementController::class, 'status'])->name('status');
                Route::get('/deleteimage/{id}', [GBGTestimonialManagementController::class, 'deleteimage'])->name('deleteimage');
                Route::any('/deleteattribute', [GBGTestimonialManagementController::class, 'deleteattribute'])->name('deleteattribute');
                Route::any('/categoryproduct', [GBGTestimonialManagementController::class, 'categoryproduct'])->name('categoryproduct');
            });
            
            Route::group(['prefix' => 'addon', 'as' => 'addon.'], function () {
                Route::any('/', [GBGAddonController::class, 'list'])->name('list');
                Route::any('/add', [GBGAddonController::class, 'add'])->name('add');
                Route::any('/edit/{id}', [GBGAddonController::class, 'edit'])->name('edit');
                Route::get('/delete/{id}', [GBGAddonController::class, 'delete'])->name('delete');
                Route::post('/status', [GBGAddonController::class, 'status'])->name('status');
                Route::get('/deleteimage/{id}', [GBGAddonController::class, 'deleteimage'])->name('deleteimage');
                Route::any('/deleteattribute', [GBGAddonController::class, 'deleteattribute'])->name('deleteattribute');
                Route::any('/categoryproduct', [GBGAddonController::class, 'categoryproduct'])->name('categoryproduct');
            });

        });

        Route::group(['prefix' => 'ordermanagement', 'as' => 'ordermanagement'],function(){
            Route::get('/',[OrderManagementController::class, 'order'])->name('order');
            Route::any('/allorder',[OrderManagementController::class, 'allorder'])->name('allorder');
            // Route::get('/gbgorder',[OrderManagementController::class, 'gbgorder'])->name('gbgorder');
            // Route::get('/gbsorder',[OrderManagementController::class, 'gbsorder'])->name('gbsorder');
        });
        Route::group(['prefix' => 'contactmanagement', 'as' => 'contactmanagement'],function(){
            Route::get('/',[ContactManagementController::class, 'contact'])->name('contact');
            Route::any('/allcontact',[ContactManagementController::class, 'allcontact'])->name('allcontact');
            Route::post('/status', [ContactManagementController::class, 'status'])->name('status');
            Route::get('/edit/{id}/{sitename}', [ContactManagementController::class, 'edit'])->name('edit');


        });
    });
});

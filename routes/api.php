<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MissingPeopleController;
use App\Http\Controllers\FoundedPeopleController;

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

Route::post('/auth/register', [AuthController::class , 'register']);
Route::post('/auth/login'   , [AuthController::class , 'login']);
Route::post('/auth/otp/generate'   , [AuthController::class , 'generateOtpCode']);
Route::post('/auth/otp/check'   , [AuthController::class , 'checkOtpCode']);
Route::post('/auth/password/reset'   , [AuthController::class , 'resetPassword']);

// TODO: add middleware 'twoFactorAuth' to the next group of routes
Route::group(['middleware' => ['auth' ,]] , function() {

    Route::group(['prefix' => 'user'  , 'as' => 'user.'] , function() {

        Route::get('/get/all'   , [UserController::class ,'getUsers'])
        ->name('get.all');
        
        Route::get('/posts/get' , [UserController::class ,'getAllUsersPostsInRandomOrder'])
        ->name('posts.get');

        Route::get('/get'       , [UserController::class ,'getUser'])
        ->name('get');

        Route::post('/update'   , [UserController::class ,'updateUserAccount'])
        ->name('update.account');

        Route::post('/delete'   , [UserController::class ,'deleteUserAccount'])
        ->name('delete.account');

    });
    Route::group(['prefix' => 'missingPerson'  , 'as' => 'missingPerson.'] , function() {

        Route::post('/add'          , [MissingPeopleController::class ,'addMissingPerson'])
        ->name('add');

        Route::get('/get/all'       , [MissingPeopleController::class ,'getMissingPeople'])
        ->name('get.all');
        
        Route::post('/update'       , [MissingPeopleController::class ,'updateMissingPersonData'])
        ->name('update');
        
        Route::post('/delete/{id}'  , [MissingPeopleController::class ,'deleteMissingPersonData'])
        ->name('delete');

    });
    Route::group(['prefix' => 'foundedPerson'  , 'as' => 'foundedPerson.'] , function() {

        Route::post('/add'          , [FoundedPeopleController::class ,'addFoundedPerson'])
        ->name('add');

        Route::get('/get/all'       , [FoundedPeopleController::class ,'getFoundedPeople'])
        ->name('get.all');

        Route::post('/update'       , [FoundedPeopleController::class ,'updateFoundedPersonData'])
        ->name('update');

        Route::post('/delete/{id}'   , [FoundedPeopleController::class ,'deleteFoundedPersonData'])
        ->name('delete');
        
    });
});

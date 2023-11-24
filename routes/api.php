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

Route::post('/auth/register' , [AuthController::class , 'register']);
Route::post('/auth/login' , [AuthController::class , 'login']);

Route::group(['middleware' => 'auth'] , function() {
    Route::group(['prefix' => 'user'  , 'as' => 'user.'] , function() {
        Route::get('/get/all', [UserController::class ,'getUsers'])->name('get.all');
        Route::get('/get', [UserController::class ,'getUser'])->name('get');
        Route::post('/update', [UserController::class ,'updateUserAccount'])->name('update.account');
        Route::post('/delete', [UserController::class ,'deleteUserAccount'])->name('delete.account');
    });
    Route::group(['prefix' => 'missingPerson'  , 'as' => 'missingPerson.'] , function() {
        Route::get('/get/all', [MissingPeopleController::class ,'getMissingPeople'])->name('get.all');
        Route::get('/add', [MissingPeopleController::class ,'addMissingPerson'])->name('add');
        Route::post('/update', [MissingPeopleController::class ,'updateMissingPerson'])->name('update');
        Route::post('/delete', [MissingPeopleController::class ,'deleteMissingPerson'])->name('delete');
    });
    Route::group(['prefix' => 'foundedPerson'  , 'as' => 'foundedPerson.'] , function() {
        Route::get('/get/all', [FoundedPeopleController::class ,'getFoundedPeople'])->name('get.all');
        Route::get('/add', [FoundedPeopleController::class ,'addFoundedPerson'])->name('add');
        Route::post('/update', [FoundedPeopleController::class ,'updateFoundedPerson'])->name('update');
        Route::post('/delete', [FoundedPeopleController::class ,'deleteFoundedPerson'])->name('delete');
    });
});

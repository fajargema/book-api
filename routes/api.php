<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['api', 'jwt.auth']], function () {
        Route::middleware('jwt.auth')->post('logout', [AuthController::class, 'logout']);

        Route::get('book/get', [BookController::class, 'index']);
        Route::get('book/get/{id}', [BookController::class, 'show']);
        Route::post('book/create', [BookController::class, 'store']);
        Route::put('book/update/{id}', [BookController::class, 'update']);
        Route::delete('book/delete/{id}', [BookController::class, 'destroy']);
    });
});

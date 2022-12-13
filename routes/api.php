<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('book')->group(function(){
    Route::get('index', [BookController::class, 'index']);
});
Route::prefix('book')->group(function(){
    Route::post('store', [BookController::class, 'store']);
});
Route::prefix('book')->group(function(){
    Route::put('update/{id}', [BookController::class, 'update']);
});
Route::prefix('book')->group(function(){
    Route::delete('delete/{id}', [BookController::class, 'delete']);
});
Route::prefix('book')->group(function(){
    Route::get('show/{id}', [BookController::class, 'show']);
});
//author
Route::prefix('author')->group(function(){
    Route::get('index', [AuthorController::class, 'index']);
});
Route::prefix('author')->group(function(){
    Route::post('store', [AuthorController::class, 'store']);
});
Route::prefix('author')->group(function(){
    Route::put('update/{id}', [AuthorController::class, 'update']);
});
Route::prefix('author')->group(function(){
    Route::delete('delete/{id}', [AuthorController::class, 'delete']);
});
Route::prefix('author')->group(function(){
    Route::get('show/{id}', [AuthorController::class, 'show']);
});

//Authentication is not required for these endpoints
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

//Authentication is required for these endpoints (apply middleware auth:sanctum)
Route::group(['middleware' => ["auth:sanctum"]], function () {
    Route::get('userProfile', [AuthController::class, 'userProfile']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::put('changePassword', [AuthController::class, 'changePassword']);
    Route::prefix('book')->group(function(){
        Route::post('addBookReview', [BookController::class, 'addBookReview']);
    });
    Route::prefix('book')->group(function(){
        Route::put('updateBookReview/{id}', [BookController::class, 'updateBookReview']);
    });


});

// Route::post('addBookReview', [AuthorController::class, 'addBookReview']);

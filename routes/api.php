<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/v1')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::middleware([
            'auth:sanctum',
            'role:Admin'
        ])
            ->post('/register/admin', [AuthController::class, 'registerAsAdmin']);
    });

    Route::apiResources([
        'authors' => AuthorController::class,
        'books' => BookController::class,
        'categories' => CategoryController::class,
        'tags' => TagController::class,
        'users' => UserController::class,
    ]);

    Route::prefix('/file')->group(function () {
        Route::get('/public/{id}', [FileController::class, 'streamAsPublic']);
        Route::get('/private/{id}', [FileController::class, 'streamAsPrivate']);
    });
});

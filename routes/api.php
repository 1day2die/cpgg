<?php

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ServerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VoucherController;
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

Route::middleware('api.token')->group(function () {
    Route::resource('users', UserController::class)->except(['create']);
    Route::patch('/users/{user}/increment', [UserController::class, 'increment']);
    Route::patch('/users/{user}/decrement', [UserController::class, 'decrement']);
    Route::patch('/users/{user}/suspend', [UserController::class, 'suspend']);
    Route::patch('/users/{user}/unsuspend', [UserController::class, 'unsuspend']);

    Route::patch('/servers/{server}/suspend', [ServerController::class, 'suspend']);
    Route::patch('/servers/{server}/unsuspend', [ServerController::class, 'unSuspend']);
    Route::resource('servers', ServerController::class)->except(['store', 'create', 'edit', 'update']);

    Route::resource('vouchers', VoucherController::class)->except('create', 'edit');

    Route::resource('roles', RoleController::class);

    Route::resource('products', ProductController::class);

    Route::scopeBindings()->group(function () {
        Route::get('/notifications/{user}', [NotificationController::class, 'index'])->withoutScopedBindings();
        Route::get('/notifications/{user}/{notification}', [NotificationController::class, 'view']);
        Route::post('/notifications/send-to-all', [NotificationController::class, 'sendToAll'])->withoutScopedBindings();
        Route::post('/notifications/send-to-users', [NotificationController::class, 'sendToUsers'])->withoutScopedBindings();
        Route::delete('/notifications/{user}/{notification}', [NotificationController::class, 'deleteOne']);
        Route::delete('/notifications/{user}', [NotificationController::class, 'delete'])->withoutScopedBindings();
    });
});

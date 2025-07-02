<?php
/**
 * Class routes.php | Date: 24.02.2024
 * Created by Krzysztof Haller
 *
 * All rights reserved 2024 khaller.com
 *
 * Redistribution and use in source and binary forms, with or without modification, are not permitted without the express permission of Krzysztof Haller
 *
 * Contact: contact@khaller.com
 *
 * Website: https://khaller.com
 *
 */

use App\Extensions\System\KServerManagement\Controllers\BackupsController;
use App\Extensions\System\KServerManagement\Controllers\DatabasesController;
use App\Extensions\System\KServerManagement\Controllers\FileManagerController;
use App\Extensions\System\KServerManagement\Controllers\IndexController;
use App\Extensions\System\KServerManagement\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('server-management/{server}')->name('kservermanagement.')->group(function () {
    Route::get('/', IndexController::class)->name('index');
    Route::get('/websocket', [IndexController::class, 'websocketToken'])->name('websocket');

    Route::prefix('/files')->name('filemanager.')->controller(FileManagerController::class)->group(function () {
        Route::get('/', '__invoke')->name('index');
        Route::get('/upload/', 'uploadUrl')->name('upload');
        Route::get('/delete/', 'deleteFile')->name('remove');
        Route::get('/download/', 'downloadFile')->name('download');
        Route::post('/content/', 'getFileContents')->name('content');
        Route::post('/save/', 'updateFile')->name('save');
        Route::get('/compress/', 'compress')->name('compress');
    });

    Route::prefix('/databases')->name('databases.')->controller(DatabasesController::class)->group(function () {
        Route::get('/', '__invoke')->name('index');
        Route::post('/create/', 'create')->name('create');
        Route::get('/delete/{databaseId}', 'delete')->name('delete');
        Route::get('/reset/{databaseId}', 'rotate')->name('reset');
    });

    Route::prefix('/backups')->name('backups.')->controller(BackupsController::class)->group(function () {
        Route::get('/', '__invoke')->name('index');
        Route::get('/delete/{backupId}','delete')->name('delete');
        Route::get('/create/', 'create')->name('create');
        Route::get('/download/{backupId}', 'download')->name('download');
    });

    Route::prefix('/settings')->name('settings.')->controller(SettingsController::class)->group(function () {
        Route::get('/', '__invoke')->name('index');
        Route::post('/', 'update')->name('update');
    });
});

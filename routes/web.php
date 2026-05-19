<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | CHAT PRIVATE
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard/{user?}', [MessageController::class, 'index'])
        ->name('dashboard');

    Route::post('/messages/send', [MessageController::class, 'send'])
        ->name('messages.send');

    Route::get('/messages/{user}', [MessageController::class, 'getMessages'])
        ->name('messages.get');

    /*
    |--------------------------------------------------------------------------
    | GROUP CHAT
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard/group/{group}', [GroupController::class, 'show'])
        ->name('dashboard.group');

    Route::get('/groups', [GroupController::class, 'index'])
        ->name('groups');

    Route::get('/groups/{group}', [GroupController::class, 'show'])
        ->name('groups.show');

    Route::post('/groups/send', [GroupController::class, 'send'])
        ->name('groups.send');

    Route::get('/groups/messages/{group}', [GroupController::class, 'getMessages'])
        ->name('groups.messages');

    /*
    |--------------------------------------------------------------------------
    | ONLINE SYSTEM
    |--------------------------------------------------------------------------
    */

    Route::post('/set-online', function () {

        if (auth()->check()) {
            auth()->user()->update([
                'is_online' => true,
                'last_seen' => now(),
            ]);
        }

        return response()->json(['success' => true]);

    })->middleware('auth');


    Route::post('/set-offline', function () {

        if (auth()->check()) {
            auth()->user()->update([
                'is_online' => false,
                'last_seen' => now(),
            ]);
        }

        return response()->json(['success' => true]);

    })->middleware('auth');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
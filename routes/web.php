<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

// ROUTE TAMBAHAN
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'loginPost']);

Route::get('/register', [AuthController::class, 'register']);
Route::post('/register', [AuthController::class, 'registerPost']);

Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {

    Route::get('/chat', [ChatController::class, 'index']);

    Route::get('/chat/{id}', [ChatController::class, 'chat']);

    // GROUP CHAT
    Route::get(
        '/chat/group/{id}',
        [ChatController::class, 'groupChat']
    );

    // PRIVATE MESSAGE
    Route::post('/send-message', [ChatController::class, 'send']);

    Route::get('/messages/{id}', [ChatController::class, 'getMessages']);

    // GROUP MESSAGE
    Route::post(
        '/send-group-message',
        [ChatController::class, 'sendGroupMessage']
    );

    Route::get(
        '/group-messages/{id}',
        [ChatController::class, 'getGroupMessages']
    );

});

Route::get('/online-status', function () {

    $user = \App\Models\User::find(auth()->id());

    $user->is_online = true;

    $user->save();

    return response()->json([
        'success' => true
    ]);

})->middleware('auth');

Route::get('/users-status', function () {

    return \App\Models\User::where(
        'id',
        '!=',
        auth()->id()
    )->get();

})->middleware('auth');
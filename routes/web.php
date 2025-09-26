<?php

use App\Http\Controllers\GroupChatController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name("welcome");


Route::middleware(['auth'])->group(function () {
    // group chat routes
    Route::get('/create-group-form',  [GroupChatController::class, 'create'])->name('groupChat.create');
    Route::post('/create-group/{name}', [GroupChatController::class, 'store'])->name('groupChat.store');
    Route::get('/group-chat/{id}', [GroupChatController::class, 'show'])->name('groupChat.show');
    Route::get('/group-chats', [GroupChatController::class, 'index'])->name('groupChat.index');
    Route::post('/group-chats/{id}', [GroupChatController::class, 'join'])->name('groupChat.join');
    Route::delete('/group-chats/{id}', [GroupChatController::class, 'leave'])->name('groupChat.leave');

    // message routes
    Route::post('/group-chats/{groupChatId}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/group-chats/{id}/messages', [MessageController::class, 'index'])->name('groupChats.messages');
});





require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyBarearToken;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\UsersFormsController;
use App\Http\Controllers\RespostasController;

Route::get('/', function () {
    return view('welcome');
});




// Aplicar o middleware VerifyBarearToken para todas as rotas exceto users.create
Route::middleware(VerifyBarearToken::class)->group(function () {
    Route::post('/create-user', [UsersFormsController::class, 'create'])->name('users.create');
});

Route::get('/show-user', [UsersFormsController::class, 'show']) ->name('users.show');





Route::post('/create-forms', [FormsController::class, 'create']) ->name('forms.create');
Route::get('/show-forms', [FormsController::class, 'show']) ->name('forms.show');
Route::get('/list-forms', [FormsController::class, 'list']) ->name('forms.list');

Route::post('/create-perguntas', [FormsController::class, 'create_perguntas']) ->name('forms.create');

Route::post('/enviar-post-forms', [FormsController::class, 'enviarPost']) ->name('forms.list');

Route::post('/create-respostas', [RespostasController::class, 'create']) ->name('respostas.create');
Route::get('/list-respostas', [RespostasController::class, 'list']) ->name('respostas.list');




Route::fallback(function () {
    return response()->json(['error' => 'Rota não encontrada.'], 404);
});
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FormsController;
use App\Http\Controllers\UsersFormsController;
use App\Http\Controllers\RespostasController;

Route::get('/', function () {
    return view('welcome');
});




Route::post('/create-forms', [FormsController::class, 'create']) ->name('forms.create');

Route::post('/create-perguntas', [FormsController::class, 'create_perguntas']) ->name('forms.create');

Route::post('/store-forms', [FormsController::class, 'store']) ->name('forms.store');
Route::get('/show-forms', [FormsController::class, 'show']) ->name('forms.show');
Route::get('/list-forms', [FormsController::class, 'list']) ->name('forms.list');

Route::post('/enviar-post-forms', [FormsController::class, 'enviarPost']) ->name('forms.list');


Route::post('/create-user', [UsersFormsController::class, 'create']) ->name('users.create');
Route::get('/show-user', [UsersFormsController::class, 'show']) ->name('users.show');

Route::post('/create-respostas', [RespostasController::class, 'create']) ->name('respostas.create');
Route::get('/list-respostas', [RespostasController::class, 'list']) ->name('respostas.list');

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
 
    return ['token' => $token->plainTextToken];
});


Route::fallback(function () {
    return response()->json(['error' => 'Rota não encontrada.'], 404);
});
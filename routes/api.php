<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function () {
    Route::post('/auth/token', [UserController::class, 'getToken']);
    //Necessário passar parâmetro "api_token" (token recebido no '/auth/token) para as rotas seguintes
    Route::middleware('auth:api')->get('/livros', [LivroController::class, 'list']);
    Route::middleware('auth:api')->post('/livros', [LivroController::class, 'store']);
    Route::middleware('auth:api')->post('/livros/{livroId}/importar-indices-xml', [LivroController::class, 'importIndexesFromXml']);
});

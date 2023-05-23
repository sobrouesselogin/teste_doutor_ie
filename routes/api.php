<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LivroController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Route::post('/auth/token', [ProductController::class, 'store']);
    Route::get('/livros', [LivroController::class, 'list']);
    Route::post('/livros', [LivroController::class, 'store']);
    Route::post('/livros/{livroId}/importar-indices-xml', [LivroController::class, 'importIndexesFromXml']);
});

// POST v1/auth/token Recuperar token de acesso do usuário para poder acessar as
// outras rotas
// GET v1/livros Listar livros
// POST v1/livros Cadastrar livro.
// POST v1/livros/{livroId}/importar-indices-xml Importar índices em xml
// Cada rota de livro está documentada nas páginas seguintes.
// GET v1/livros
// - descrição: Listar livros
// - query params:
// - titulo: filtrar por titulo do livro
// - titulo_do_indice: retornar livro que possui o índice com o título pesquisado juntamente
// com os seus ascendentes, quando houver.
// - response:
// Exemplo, se pesquisar o título do índice Beta deve retornar o seguinte resultado
// POST v1/livros
// - descrição: Cadastrar livro, validar estrutura dos índices
// - request body:
// POST v1/livros/{livroId}/importar-indices-xml
// Requisito: Criar job para importação do XM
<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Indice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LivroController extends Controller
{
    /**
     * List all books.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {
            DB::transaction(function () use ($request, &$books) {
                $booksQuery = Livro::query();

                //filters
                if ($request->titulo_do_indice) {
                    $indexesQuery = DB::table('indices')->where('titulo', 'LIKE', '%'.$request->titulo_do_indice.'%');
                    $indexes = $indexesQuery->get();
                    $bookIds = [];

                    if (!empty($indexes)) {
                        foreach ($indexes as $index) {
                            $bookIds[] = $index->livro_id;
                        }

                        $booksQuery->whereIn('id', $bookIds);
                    } else {
                        //índice inexistente, retorna array vazio
                        $booksQuery->whereIn('id', [-1]);
                    }
                }

                if ($request->titulo) {
                    $booksQuery->where('titulo', 'LIKE', '%'.$request->titulo.'%');
                }

                $books = $booksQuery
                    ->with('indices')
                    ->with('usuarioPublicador')
                    ->with('indices.subIndices')
                    ->get();
            });

            return response()->json([
                'message'=>'',
                'result'=>$books->toJson()
            ], 200); 
        } catch (\Throwable $th) {
            throw $th;

            return response()->json([
                'message'=>'Erro ao listar livros',
                'result'=>null
            ], 200); 
        }

        return view('product.list', ['products' => $products]);
    }

    /**
     * Store a new book.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $insertedBook = Livro::create([
                    //TODO, auth token
                    'usuario_publicador_id' => $request->usuario_publicador_id,
                    'titulo' => $request->titulo
                ]);

                //save indices relation
                $indexesArray = json_decode($request->indices, true);
                $indexesToBeInserted = [];
                foreach ($indexesArray as $index) {
                    //$subIndexesObject = self::getSubIndexesObjectsRecursively($index['subindices']);

                    $indexToBeInserted = new Indice([
                        'titulo' => $index['titulo'], 
                        'pagina' => $index['pagina'],
                        // 'indices' => $subIndexesObject
                    ]);
                    $insertedBook->indices()->save($indexToBeInserted);

                    //salva os subindices
                    if (array_key_exists("subindices", $index)) {
                        foreach ($index['subindices'] as $subIndex) {
                            $subIndexToBeInserted = new Indice([
                                'titulo' => $subIndex['titulo'], 
                                'pagina' => $subIndex['pagina'],
                                'indice_pai_id' => $indexToBeInserted->id,
                                'livro_id' => $insertedBook->id
                            ]);
                            $indexToBeInserted->indices()->save($subIndexToBeInserted);
                        }
                    }

                    //TODO: função recursiva para aceita níveis ilimitados de subindices
                    //self::saveSubIndexesRecursively($index['subindices']);
                }
                //$insertedBook->indices()->saveMany($indexesToBeInserted);

                //update model with relations
                $insertedBook->refresh();

            });

            return response()->json(['message'=>'Inserido com sucesso'], 200); 
        } catch (\Throwable $th) {
            throw $th;

            return response()->json(['message'=>'Erro ao inserir'], 200); 
        }
    }

    //TODO: função recursiva para aceita níveis ilimitados de subindices
    private static function saveSubIndexesRecursively($subIndexesArray) {
        if (empty($subIndexesArray)) {
            return [];
        }

        $subIndexesObjectArray = [];
        foreach ($subIndexesArray as $subIndex) {
            $subIndexObject = new Indice([
                'titulo' => $subIndex['titulo'], 
                'pagina' => $subIndex['pagina'],
                'indice_pai_id' => $subIndex['pagina'],
                'indices' => self::getSubIndexesObjectsRecursively($subIndex['subindices'])
            ]);
        }
        $subIndexesObjectArray[] = $subIndexObject;

        return $subIndexesObjectArray;
    }
    
}

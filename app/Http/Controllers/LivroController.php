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
                
                self::saveIndexesRecursively($indexesArray, $insertedBook->id, $parentIndexId = null);
            });

            return response()->json(['message'=>'Inserido com sucesso'], 200); 
        } catch (\Throwable $th) {
            throw $th;

            return response()->json(['message'=>'Erro ao inserir'], 200); 
        }
    }

    private static function saveIndexesRecursively($indexesArray, $bookId, $parentIndexId = null) {
        if (empty($indexesArray)) {
            return;
        }

        //iterate items
        foreach ($indexesArray as $index) {
            $index['livro_id'] = $bookId;
            if (!is_null($parentIndexId)) {
                $index['indice_pai_id'] = $parentIndexId;
            }
            $insertedIndex = Indice::create($index);
            
            self::saveIndexesRecursively($index['subindices'], $bookId, $insertedIndex->id);
        }
    }

    /**
     * Import indexes from xml body.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $bookId Id of the book
     * @return \Illuminate\Http\Response
     */
    public function importIndexesFromXml(Request $request, $bookId) {
        DB::transaction(function () use ($request, $bookId) {
            $book = Livro::find($bookId);

            $xmlString = $request->getContent();
            $xmlObject = simplexml_load_string($xmlString);

            self::saveXmlIndexesRecursively($xmlObject->item, $bookId);
        });
    }

    private static function saveXmlIndexesRecursively($itemsArray, $bookId, $parentIndexId = null) {
        if (empty($itemsArray)) {
            return;
        }

        //iterate items
        foreach ($itemsArray as $item) {
            $indexValues = current($item->attributes());
            $indexValues['livro_id'] = $bookId;
            if (!is_null($parentIndexId)) {
                $indexValues['indice_pai_id'] = $parentIndexId;
            }
            $insertedIndex = Indice::create($indexValues);
            
            self::saveXmlIndexesRecursively($item->children(), $bookId, $insertedIndex->id);
        }
    }
}

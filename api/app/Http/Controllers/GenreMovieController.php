<?php

namespace App\Http\Controllers;

use App\Models\GenreMovies;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GenreMovieController extends Controller
{

    public function __construct(GenreMovies $genres_movie)
    {
        $this->genres_movie = $genres_movie;
    }

    public function showAllGenreMovies(Request $request)
    {
        try {
            $query = $this->genres_movie->query();

            if($request->has('ativo')){
                $ativo = filter_var($request->ativo, FILTER_VALIDATE_BOOLEAN);

                if ($ativo) {
                    $query->whereNull('deleted_at');
                } else {
                    $query->onlyTrashed();
                }
            } 
            
            if (!empty($request->name)) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
    
            $genre_movie = $query->get();
    
            if ($genre_movie->isEmpty()) {
                $return = ["message" => "Nenhum gênero encontrado!", "success" => true];
            } else {
                $return = ["message" => "Gêneros encontrados com sucesso!", "genre_movie" => $genre_movie, "success" => true];
            }
    
            return response()->json($return);
        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar os gêneros!", "message" => $th->getMessage(), "success" => false];
            return response()->json($return);
        }
    }
    


    public function showOneGenreMovie($id)
    {
        try {
            $genre_movie = GenreMovies::find($id); 

            if($genre_movie){
                $return = ["message" => "Gênero encontrado com sucesso!", "genre_movie" => $genre_movie, "success" => true ];
                return response()->json($return);
            } else {
                $return = ["message" => "Gênero não encontrado!", "success" => false ];
                return response()->json($return);
            }
        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar o gênero!","message" => $th->getMessage(),"success" => false];
            return response()->json($return);
        }
    }

    public function create(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:genre_movies,name|max:255',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }

            $genre_movie = GenreMovies::create([
                'name' => $request->input('name'),
            ]);

            return ["message" => "Gênero criado com sucesso!", "genre_movie" => $genre_movie, "success" => true];

        } catch (\Throwable $th) {
            return ["error" => "Erro ao criar o gênero!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function delete($id)
    {
        try {
            $streaming = GenreMovies::findOrFail($id);
    
            $streaming->delete();
    
            return ["message" => "Gênero deletado com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao deletar o gênero!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function restore($id){

        try {
            $streaming = GenreMovies::withTrashed()->findOrFail($id);
    
            $streaming->restore();
    
            return ["message" => "Gênero restaurado com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao restaurar o gênero!", "message" => $th->getMessage(), "success" => false];
        }
    }



    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => [
                    'sometimes',
                    'string',
                    'max:255',
                    Rule::unique('genre_movies', 'name')->ignore($id),
                ]
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }
    
            $streaming = GenreMovies::findOrFail($id);
    

            if ($streaming) {
                $streaming->update([
                    'name' => $request->input('name') ?? $streaming->name,
                ]);
    
                return ["message" => "Gênero atualizado com sucesso!", "streaming" => $streaming, "success" => true];
            } else {
                return ["message" => "Gênero não encontrado!", "success" => false];
            }
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao atualizar o gênero!", "message" => $th->getMessage(), "success" => false];
        }
    }
    
    

}

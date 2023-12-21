<?php

namespace App\Http\Controllers;

use App\Models\Streaming;
use App\Models\MovieStreaming;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovieStreamingController extends Controller
{

    public function __construct(MovieStreaming  $moviestreaming)
    {
        $this->moviestreaming = $moviestreaming;
    }

    public function addMovieStreaming($id, $id_streaming)
    {
        try {
            $movie = Movie::findOrFail($id);
            $streaming = Streaming::findOrFail($id_streaming);
    
            if (!$movie->streamings()->where('streaming_id', $id_streaming)->exists()) {
                $movie->streamings()->attach($streaming);
                return ["message" => "Filme adicionado ao streaming com sucesso!", "success" => true];
            } else {
                return ["message" => "O filme já está associado a este streaming!", "success" => false];
            }
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao adicionar o filme ao streaming!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function showAllMoviesInStreamings(Request $request)
{
    try {
        $query = $this->moviestreaming->with(['genreMovies', 'streaming']);   

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

        // if (!empty($request->streaming_id)){
        //     $query->wherehas('streaming', function (Builder $query) use ($request) {
        //         $query->where('id', $request->streaming_id);
        //     });
        // }

        if (!empty($request->genre_movie_id)){
            $query->wherehas('genres', function (Builder $query) use ($request) {
                $query->where('id', $request->genre_movie_id);
            });
        }

        if (!empty($request->synopsis)) {
            $query->where('synopsis', 'like', '%' . $request->synopsis . '%');
        }

        if (!empty($request->month_release)) {
            $query->where('month_release', $request->month_release);
        }

        if (!empty($request->year_release)) {
            $query->where('year_release', $request->year_release);
        }


        $movies = $query->get();

        if ($movies->isEmpty()) {
            $return = ["message" => "Nenhum filme encontrado!", "success" => true];
        } else {
            $return = ["message" => "Filmes encontrados com sucesso!", "movies" => $movies, "success" => true];
        }

        return response()->json($return);
    } catch (\Throwable $th) {
        $return = ["error" => "Erro ao buscar filmes nos streamings!", "message" => $th->getMessage(), "success" => false];
        return response()->json($return);
    }
}

public function deleteMovieStreaming($id, $id_streaming)
{
    try {
        // Implemente a lógica para excluir um filme de um streaming específico
        // Use os IDs fornecidos para localizar e remover a associação
        // Retorne uma mensagem apropriada de acordo com o resultado da operação
        // Exemplo: return ["message" => "Operação de exclusão de filme em streaming", "success" => true];
    } catch (\Throwable $th) {
        return ["error" => "Erro ao excluir filme do streaming!", "message" => $th->getMessage(), "success" => false];
    }
}

public function update(Request $request)
{
    try {
        // Implemente a lógica para atualizar os dados do filme em um streaming
        // Receba os dados necessários do corpo da requisição
        // Realize a atualização conforme os parâmetros fornecidos e retorne a resposta adequada
        // Exemplo: return ["message" => "Operação de atualização de filme em streaming", "success" => true];
    } catch (\Throwable $th) {
        return ["error" => "Erro ao atualizar filme no streaming!", "message" => $th->getMessage(), "success" => false];
    }
}

public function restore($id, $id_streaming)
{
    try {
        // Implemente a lógica para restaurar um filme em um streaming
        // Use os IDs fornecidos para realizar a restauração, se aplicável
        // Retorne uma mensagem apropriada de acordo com o resultado da operação
        // Exemplo: return ["message" => "Operação de restauração de filme em streaming", "success" => true];
    } catch (\Throwable $th) {
        return ["error" => "Erro ao restaurar filme no streaming!", "message" => $th->getMessage(), "success" => false];
    }
}


    

}

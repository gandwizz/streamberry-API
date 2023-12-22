<?php

namespace App\Http\Controllers;

use App\Models\Movie;
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
         $query = MovieStreaming::with([
            'streamings', 
            'movies', 
            'movies.genres',
            'movies.assessments',
        ]);

            if($request->has('ativo')){
                $ativo = filter_var($request->ativo, FILTER_VALIDATE_BOOLEAN);

                if ($ativo) {
                    $query->whereNull('deleted_at');
                } else {
                    $query->onlyTrashed();
                }
            } 


            if (!empty($request->movie_id)) {
                $query->where('movie_id',  $request->movie_id);
            }

            if (!empty($request->streaming_id)) {
                $query->where('streaming_id',  $request->streaming_id);
            }


            $query->when($request->has('month_release'), function ($query) use ($request) {
                $query->whereHas('movies', function (Builder $query) use ($request) {
                    $query->where('month_release', $request->month_release);
                });
            });

            $query->when($request->has('year_release'), function ($query) use ($request) {
                $query->whereHas('movies', function (Builder $query) use ($request) {
                    $query->where('year_release', $request->year_release);
                });
            });

            $query->when($request->has('assessment'), function ($query) use ($request) {
                $query->whereHas('movies', function (Builder $query) use ($request) {
                    $query->whereHas('assessments', function (Builder $query) use ($request) {
                        $query->where('assessment', $request->assessment);
                    });
                });
            });

            $query->when($request->has('comment'), function ($query) use ($request) {
                $query->whereHas('movies', function (Builder $query) use ($request) {
                    $query->whereHas('assessments', function (Builder $query) use ($request) {
                        $query->where('comment', 'like', '%' . $request->comment . '%');
                    });
                });
            });

            $result = $query->get();

            if ($result->isEmpty()) {
                $return = ["message" => "Nenhum filme encontrado!", "success" => false];
            } else {
                $return = ["message" => "Filmes encontrados com sucesso!", "movies" => $result, "success" => true];
            }

            return response()->json($return);
        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar filmes nos streamings!", "message" => $th->getMessage(), "success" => false];
            return response()->json($return);
        }
    }



    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'movie_id' => 'required|integer',
                'streaming_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }

            $movieStreaming = MovieStreaming::findOrFail($id);

            if (!$movieStreaming) {
                return ["message" => "Relação filme e streaming não encontrada!", "success" => false];
            }

            $existingRelation = MovieStreaming::where('movie_id', $request->movie_id)
                ->where('streaming_id', $request->streaming_id)
                ->where('id', '!=', $id) 
                ->exists();

            if ($existingRelation) {
                return ["message" => "Essa relação filme e streaming já existe!", "success" => false];
            }

            $movieStreaming->update($request->all());

            return ["message" => "Relação filme e streaming atualizada com sucesso!", "streaming" => $movieStreaming, "success" => true];

        } catch (\Throwable $th) {
            return ["error" => "Erro ao atualizar filme no streaming!", "message" => $th->getMessage(), "success" => false];
        }
    }

public function deleteMovieStreaming($id, $id_streaming)
{
    try {

        $movie = MovieStreaming::where('movie_id', $id)->where('streaming_id', $id_streaming)->first();

        if ($movie){
            $movie->delete();
            return ["message" => "Filme excluído do streaming com sucesso!", "success" => true];
        } else {
            return ["message" => "O filme não está associado a este streaming!", "success" => false];
        }

    } catch (\Throwable $th) {
        return ["error" => "Erro ao excluir filme do streaming!", "message" => $th->getMessage(), "success" => false];
    }
}


public function restore($id, $id_streaming)
{
    try {
        $movie = MovieStreaming::withTrashed()->where('movie_id', $id)->where('streaming_id', $id_streaming)->first();

        if ($movie){
            $movie->restore();
            return ["message" => "Filme restaurado no streaming com sucesso!", "success" => true];
        } else {
            return ["message" => "O filme não está associado a este streaming!", "success" => false];
        }
    } catch (\Throwable $th) {
        return ["error" => "Erro ao restaurar filme no streaming!", "message" => $th->getMessage(), "success" => false];
    }
}

}

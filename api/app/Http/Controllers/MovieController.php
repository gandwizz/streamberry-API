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
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{

    public function __construct(Movie $movie, MovieStreaming  $moviestreaming)
    {
        $this->movie = $movie;
        $this->moviestreaming = $moviestreaming;
    }

    public function showAllMovies(Request $request)
    {
        try {
            $query = $this->movie->with(['genres', 'assessments']);   

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

  
            if (!empty($request->assessment)) {
                $query->whereHas('assessments', function (Builder $query) use ($request) {
                    $query->where('assessment', $request->assessment);
                });
            }
    
            // Adicionando filtro para comment
            if (!empty($request->comment)) {
                $query->whereHas('assessments', function (Builder $query) use ($request) {
                    $query->where('comment', 'like', '%' . $request->comment . '%');
                });
            }

    
            $movies = $query->get();
    
            if ($movies->isEmpty()) {
                $return = ["message" => "Nenhum filme encontrado!", "success" => true];
            } else {
                $return = ["message" => "Filmes encontrados com sucesso!", "movies" => $movies, "success" => true];
            }
    
            return response()->json($return);
        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar os filmes!", "message" => $th->getMessage(), "success" => false];
            return response()->json($return);
        }
    }
    


    public function showOneMovie($id)
    {
        try {
            $movie = Movie::find($id);


            if($movie){
                $return = ["message" => "Filme encontrado com sucesso!", "movie" => $movie, "success" => true ];
                return response()->json($return);
            }else{
                $return = ["message" => "Filme não encontrado!", "success" => false ];
                return response()->json($return);
            }

        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar o filme!","message" => $th->getMessage(),"success" => false];
            return response()->json($return);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:streamings,name|max:255',
                'genre_movie_id' => 'required|integer ',
                'month_release' => 'required|integer ',
                'year_release' => 'required|integer '
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }

            $movie = Movie::create([
                'name' => $request->input('name'),
                'genre_movie_id' => $request->input('genre_movie_id'),
                'synopsis' => $request->input('synopsis'),
                'month_release' => $request->input('month_release'),
                'year_release' => $request->input('year_release')
            ]);

            return ["message" => "Filme criado com sucesso!", "movie" => $movie, "success" => true];

        } catch (\Throwable $th) {
            return ["error" => "Erro ao criar o filme!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function delete($id)
    {
        try {
            $movie = Movie::findOrFail($id);
    
            $movie->delete();
    
            return ["message" => "Filme deletado com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao deletar o filme!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function restore($id){

        try {
            $movie = Movie::withTrashed()->findOrFail($id);
    
            $movie->restore();
    
            return ["message" => "Filme restaurado com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao restaurar o filme!", "message" => $th->getMessage(), "success" => false];
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
                    Rule::unique('movies', 'name')->ignore($id),
                ],
                'name' => 'sometimes|string',
                'genre_movie_id' => 'sometimes|integer ',
                'month_release' => 'sometimes|integer ',
                'year_release' => 'sometimes|integer '
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }
    
            $movie = Movie::findOrFail($id);
    

            if ($movie) {

                $movie->fill($request->all());
                $movie->save();
    
                return ["message" => "Filme atualizado com sucesso!", "movie" => $movie, "success" => true];
            } else {
                return ["message" => "Filme não encontrado!", "success" => false];
            }
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao atualizar o filme!", "message" => $th->getMessage(), "success" => false];
        }
    } 
    
    
    public function averageRatingMovies($id)
    {
        try {
            $movie = Movie::with('assessments')->find($id);
    
            if ($movie) {
                $averageRating = $movie->assessments->avg('assessment');
                return [
                    "message" => "Média de avaliações do filme encontrada com sucesso!",
                    "average_rating" => $averageRating,
                    "success" => true
                ];
            } else {
                return ["message" => "Filme não encontrado!", "success" => false];
            }
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao buscar a média de avaliações do filme!", "message" => $th->getMessage(), "success" => false];
        }
    }


    // Quantos filmes e quais foram lançados em cada ano?

    public function moviesPerYear(Request $request)
    {
        try {
            $query = Movie::query();
    
            $year = $request->input('year_release');
    
            $query->when($year, function ($query) use ($year) {
                $query->where('year_release', $year);
            });
    
            $query->whereNotNull('year_release');
    
            $movies = $query->get();
    
            $groupedMovies = $movies->groupBy('year_release');
    
            // Conta o número de filmes em cada ano
            $moviesPerYear = $groupedMovies->map(function ($movies, $year) {
                return [
                    'movies' => $movies,
                    'release_year' => $year,
                    'movie_count' => count($movies),
                ];
            });
    
            $return = [
                "message" => "Contagem de filmes por ano encontrada com sucesso!",
                "movies_per_year" => $moviesPerYear,
                "success" => true,
            ];
    
            return response()->json($return);
        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar a contagem de filmes por ano!", "message" => $th->getMessage(), "success" => false];
            return response()->json($return);
        }
    }
    public function averageRatingsByGenreAndYear(Request $request)
    {
        try {
            $query = Movie::query();
    
            // Adicionando filtros opcionais para ativo, nome, gênero, sinopse, mês de lançamento e ano de lançamento
            $query->when($request->has('ativo'), function ($query) use ($request) {
                $ativo = filter_var($request->ativo, FILTER_VALIDATE_BOOLEAN);
                $query->whereNull($ativo ? 'movies.deleted_at' : 'movies.deleted_at');
            });
    
            $query->when(!empty($request->genre_movie_id), function ($query) use ($request) {
                $query->where('movies.genre_movie_id', $request->genre_movie_id);
            });
    
    
            $query->when(!empty($request->year_release), function ($query) use ($request) {
                $query->where('movies.year_release', $request->year_release);
            });
    
            $query->select('genre_movies.name as genre', DB::raw('AVG(assessments.assessment) as average_rating'))
                ->leftJoin('assessments', 'movies.id', '=', 'assessments.movie_id')
                ->leftJoin('genre_movies', 'movies.genre_movie_id', '=', 'genre_movies.id')
                ->whereNull('movies.deleted_at') // Ajuste aqui
                ->groupBy('genre_movies.name');
    
            $result = $query->get();
    
            if ($result->isEmpty()) {
                $return = ["message" => "Nenhuma avaliação encontrada!", "success" => true];
            } else {
                $return = ["message" => "Avaliações médias encontradas com sucesso!", "ratings_by_genre_and_year" => $result, "success" => true];
            }
    
            return response()->json($return);
        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar as avaliações médias!", "message" => $th->getMessage(), "success" => false];
            return response()->json($return);
        }
    }
    

    
    
    
    
    

}

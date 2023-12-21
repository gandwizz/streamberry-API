<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Streaming;
use App\Models\Assessments;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentController extends Controller
{

    public function __construct(Assessments $assessments)
    {
        $this->assessments = $assessments;
    }

    public function showAllAssessments(Request $request)
    {
        try {
            $query = $this->assessments->with(['user', 'movie.genres', 'streaming']);   

            if($request->has('ativo')){
                $ativo = filter_var($request->ativo, FILTER_VALIDATE_BOOLEAN);

                if ($ativo) {
                    $query->whereNull('deleted_at');
                } else {
                    $query->onlyTrashed();
                }
            } 

            if (!empty($request->user_id)){
                $query->wherehas('user', function (Builder $query) use ($request) {
                    $query->where('id', $request->user_id);
                });
            }

            if (!empty($request->movie_id)){
                $query->wherehas('movie', function (Builder $query) use ($request) {
                    $query->where('id', $request->movie_id);
                });
            }
            if (!empty($request->streaming_id)){
                $query->wherehas('streaming', function (Builder $query) use ($request) {
                    $query->where('id', $request->streaming_id);
                });
            }

            if (!empty($request->assessment)) {
                $query->where('assessment', $request->assessment);
            }

            if (!empty($request->comment)) {
                $query->where('comment', 'like', '%' . $request->comment . '%');
            }

            $assessments = $query->get();
    
            if ($assessments->isEmpty()) {
                $return = ["message" => "Nenhuma avaliação encontrada!", "success" => true];
            } else {
                $return = ["message" => "Avaliações encontradas com sucesso!", "assessments" => $assessments, "success" => true];
            }
    
            return response()->json($return);
        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar as avaliações!", "message" => $th->getMessage(), "success" => false];
            return response()->json($return);
        }
    }


    public function showOneAssessment($id)
    {
        try {
            $assessment = Assessments::with(['user', 'movie'])->find($id);

            if($assessment){
                $return = ["message" => "Avaliação encontrada com sucesso!", "assessment" => $assessment, "success" => true ];
                return response()->json($return);
            }else{
                $return = ["message" => "Avaliação não encontrada!", "success" => false ];
                return response()->json($return);
            }

        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar a avaliação!","message" => $th->getMessage(),"success" => false];
            return response()->json($return);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'movie_id' => 'required|integer ',
                'user_id' => 'required|integer ',
                'assessment' => 'required|integer ',
                'comment' => 'required|string '
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }

            $assessment = Assessments::create([
                'movie_id' => $request->input('movie_id'),
                'user_id' => $request->input('user_id'),
                'assessment' => $request->input('assessment'),
                'comment' => $request->input('comment')
            ]);

            return ["message" => "Avaliação criada com sucesso!", "assessment" => $assessment, "success" => true];

        } catch (\Throwable $th) {
            return ["error" => "Erro ao criar a avaliação!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function delete($id)
    {
        try {
            $assessment = Assessments::findOrFail($id);
    
            $assessment->delete();
    
            return ["message" => "Avaliação deletada com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao deletar a avaliação!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function restore($id){

        try {
            $assessment = Assessments::withTrashed()->findOrFail($id);

            $assessment->restore();
    
            return ["message" => "Avaliação restaurada com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao restaurar a avaliação!", "message" => $th->getMessage(), "success" => false];
        }
    }



    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                
                'movie_id' => 'sometimes|integer ',
                'user_id' => 'sometimes|integer ',
                'assessment' => 'sometimes|integer ',
                'comment' => 'sometimes|text '
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }
    
            $assessment = Assessments::findOrFail($id);
    

            if ($assessment) {

                $assessment->fill($request->all());
                $assessment->save();
    
                return ["message" => "Filme atualizado com sucesso!", "assessment" => $assessment, "success" => true];
            } else {
                return ["message" => "Filme não encontrado!", "success" => false];
            }
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao atualizar o filme!", "message" => $th->getMessage(), "success" => false];
        }
    }    

}

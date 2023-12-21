<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Streaming;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;

class StreamingController extends Controller
{

    public function __construct(Streaming $streaming)
    {
        $this->streaming = $streaming;
    }

    public function showAllStreamings(Request $request)
    {
        try {
            $query = $this->streaming->query();

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
    
            $streamings = $query->get();
    
            if ($streamings->isEmpty()) {
                $return = ["message" => "Nenhum streaming encontrado!", "success" => true];
            } else {
                $return = ["message" => "Streamings encontrados com sucesso!", "streamings" => $streamings, "success" => true];
            }
    
            return response()->json($return);
        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar os streamings!", "message" => $th->getMessage(), "success" => false];
            return response()->json($return);
        }
    }
    


    public function showOneStreaming($id)
    {
        try {
            $streaming = Streaming::find($id);

            if($streaming){
                $return = ["message" => "Streaming encontrado com sucesso!", "streaming" => $streaming, "success" => true ];
                return response()->json($return);
            } else {
                $return = ["message" => "Streaming não encontrado!", "success" => false ];
                return response()->json($return);
            }

        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar o streaming!","message" => $th->getMessage(),"success" => false];
            return response()->json($return);
        }
    }

    public function create(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:streamings,name|max:255',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }

            $streaming = Streaming::create([
                'name' => $request->input('name'),
            ]);

            return ["message" => "Streaming criado com sucesso!", "streaming" => $streaming, "success" => true];

        } catch (\Throwable $th) {
            return ["error" => "Erro ao criar o streaming!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function delete($id)
    {
        try {
            $streaming = Streaming::findOrFail($id);
    
            $streaming->delete();
    
            return ["message" => "Streaming deletado com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao deletar o streaming!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function restore($id){

        try {
            $streaming = Streaming::withTrashed()->findOrFail($id);
    
            $streaming->restore();
    
            return ["message" => "Streaming restaurado com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao restaurar o streaming!", "message" => $th->getMessage(), "success" => false];
        }
    }



    public function update(Request $request, $id)
    {
        try {
            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'name' => [
                    'sometimes',
                    'string',
                    'max:255',
                    Rule::unique('streamings', 'name')->ignore($id),
                ]
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }
    
            $streaming = Streaming::findOrFail($id);
    

            if ($streaming) {

                $streaming->fill($request->all());
                $streaming->save();
    
                return ["message" => "Streaming atualizado com sucesso!", "streaming" => $streaming, "success" => true];
            } else {
                return ["message" => "Streaming não encontrado!", "success" => false];
            }
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao atualizar o streaming!", "message" => $th->getMessage(), "success" => false];
        }
    }
    
    

}

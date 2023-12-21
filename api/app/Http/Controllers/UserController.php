<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function showAllUsers(Request $request)
    {
        try {
            $query = $this->user->query();
    
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
    
            if (!empty($request->email)) {
                $query->where('email', 'like', '%' . $request->email . '%');
            }
    
            $users = $query->get();
    
            if ($users->isEmpty()) {
                $return = ["message" => "Nenhum usuário encontrado!", "success" => true];
            } else {
                $return = ["message" => "Usuários encontrados com sucesso!", "users" => $users, "success" => true];
            }
    
            return response()->json($return);
        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar os usuários!", "message" => $th->getMessage(), "success" => false];
            return response()->json($return);
        }
    }
    


    public function showOneUser($id)
    {
        try {
            $user = User::find($id);

            if ($user) {

                $return = ["message" => "Usuário encontrado com sucesso!", "user" => $user, "success" => true ];
                return response()->json($return);
            }else{
                $return = ["message" => "Usuário não encontrado!", "success" => false ];
                return response()->json($return);
            }

        } catch (\Throwable $th) {
            $return = ["error" => "Erro ao buscar usuário!","message" => $th->getMessage(),"success" => false];
            return response()->json($return);
        }
    }

    public function create(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'required|min:6',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }

            $userForm = [
                "name" => $request->input('name'),
                "email" => $request->input('email'),
                "password" => app('hash')->make($request->input('password'))
            ];

            $user = User::create($userForm);
            
            return ["message" => "Usuário criado com sucesso!", "user" => $user, "success" => true];

        } catch (\Throwable $th) {
            return ["error" => "Erro ao criar usuário!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
    
            $user->delete();
    
            return ["message" => "Usuário deletado com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao deletar usuário!", "message" => $th->getMessage(), "success" => false];
        }
    }

    public function restore($id){

        try {
            $user = User::withTrashed()->findOrFail($id);
    
            $user->restore();
    
            return ["message" => "Usuário restaurado com sucesso!", "success" => true];
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao restaurar usuário!", "message" => $th->getMessage(), "success" => false];
        }
    }



    public function update(Request $request, $id)
    {
        try {
            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => [
                    'sometimes',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($id),
                ],
                'password' => 'sometimes|min:6',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Erro de validação', 'message' => $validator->errors(), 'success' => false], 422);
            }
    
            $user = User::findOrFail($id);
    
            if ($user) {
                $userForm = [
                    "name" => $request->input('name') ?? $user->name,
                    "email" => $request->input('email') ?? $user->email
                ];

                if(!empty($request->input('password'))){
                    $userForm['password'] = app('hash')->make($request->input('password'));
                }
    
                $user->update($userForm);
    
                return ["message" => "Usuário atualizado com sucesso!", "user" => $user, "success" => true];
            } else {
                return ["message" => "Usuário não encontrado!", "success" => false];
            }
    
        } catch (\Throwable $th) {
            return ["error" => "Erro ao atualizar usuário!", "message" => $th->getMessage(), "success" => false];
        }
    }
    
    

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{



    public function showAllUsers(){
        $users = User::all();
        return response()->json($users);
    }



    public function create(Request $request){

        try {
            $userForm = [
                "name" => $request->input('name'),
                "email" => $request->input('email'),
                "password" => app('has')->make($request->input('password'))
            ];
            
            $user = User::create($userForm);

            return ["message" => "Usuário criado com sucesso!", "user" => $user, "success" => true];

        } catch (\Throwable $th) {
            return ["error" => "Erro ao criar usuário!", "user" => $user, "message" => $th->getMessage(), "success" => false];
        }
    }

}

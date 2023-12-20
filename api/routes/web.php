<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//http:localhost:8000/api/users



// Rota Privada
$router->group(['middleware' => 'jwtApi'], function () use ($router) {
    
    $router->group(['prefix' => 'api'], function () use ($router) {
        $router->get('users',  ['uses' => 'UserController@showAllUsers']);
        $router->get('users/{id}', ['uses' => 'UserController@showOneUser']);
        $router->post('users', ['uses' => 'UserController@create']);
        $router->delete('users/{id}', ['uses' => 'UserController@delete']);
        $router->put('users/{id}', ['uses' => 'UserController@update']);
    });
});




Route::get('/', function () {
    return view('welcome');
});
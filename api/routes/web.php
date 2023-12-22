<?php

use Illuminate\Support\Facades\Rtendioute;

// Controllers 
use App\Http\Controllers\UserController;
use App\Http\Controllers\StreamingController;
use App\Http\Controllers\GenreMovieController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\MovieStreamingController;
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

// Rotas Publicas

    // USERS
    $router->get('users', [UserController::class, 'showAllUsers']);
    $router->get('users/{id}', [UserController::class, 'showOneUser']);
    $router->post('users', [UserController::class, 'create']);
    $router->delete('users/{id}', [UserController::class, 'delete']);
    $router->put('users/{id}', [UserController::class, 'update']);
    $router->post('users/restore/{id}', [UserController::class, 'restore']);

    // STREAMING
    $router->get('streaming', [StreamingController::class, 'showAllStreamings']);
    $router->get('streaming/{id}', [StreamingController::class, 'showOneStreaming']);
    $router->post('streaming', [StreamingController::class, 'create']);
    $router->delete('streaming/{id}', [StreamingController::class, 'delete']);
    $router->put('streaming/{id}', [StreamingController::class, 'update']);
    $router->post('streaming/restore/{id}', [StreamingController::class, 'restore']);

    // GENRE MOVIE
    $router->get('genre_movie', [GenreMovieController::class, 'showAllGenreMovies']);
    $router->get('genre_movie/{id}', [GenreMovieController::class, 'showOneGenreMovie']);
    $router->post('genre_movie', [GenreMovieController::class, 'create']);
    $router->delete('genre_movie/{id}', [GenreMovieController::class, 'delete']);
    $router->put('genre_movie/{id}', [GenreMovieController::class, 'update']);
    $router->post('genre_movie/restore/{id}', [GenreMovieController::class, 'restore']);

    // MOVIE
    $router->get('movie', [MovieController::class, 'showAllMovies']);
    $router->get('movie/{id}', [MovieController::class, 'showOneMovie']);
    $router->get('avarage-rating/{id}', [MovieController::class, 'averageRatingMovies']);
    $router->get('movies-year', [MovieController::class, 'moviesPerYear']);
    $router->get('avarage-rating-gender', [MovieController::class, 'averageRatingsByGenreAndYear']);
    $router->post('movie', [MovieController::class, 'create']);
    $router->delete('movie/{id}', [MovieController::class, 'delete']);
    $router->put('movie/{id}', [MovieController::class, 'update']);
    $router->post('movie/restore/{id}', [MovieController::class, 'restore']);

    //MOVIE - STREAMING
    $router->get('movies-streamings', [MovieStreamingController::class, 'showAllMoviesInStreamings']); 
    $router->post('movie/{id}/streaming/{id_streaming}', [MovieStreamingController::class, 'addMovieStreaming']);
    $router->delete('movie/{id}/streaming/{id_streaming}', [MovieStreamingController::class, 'deleteMovieStreaming']);
    $router->put('edit-movie-streaming', [MovieStreamingController::class, 'update']);
    // $router->post('movie/{id}/streaming/{id_streaming}', [MovieStreamingController::class, 'restore']);

    //ASSESSEMENTS
    $router->get('assessments', [AssessmentController::class, 'showAllAssessments']);
    $router->get('assessments/{id}', [AssessmentController::class, 'showOneAssessment']);
    $router->post('assessments', [AssessmentController::class, 'create']);
    $router->delete('assessments/{id}', [AssessmentController::class, 'delete']);
    $router->put('assessments/{id}', [AssessmentController::class, 'update']);
    $router->post('assessments/restore/{id}', [AssessmentController::class, 'restore']);


// Rotas Privadas
$router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router) {


});


Route::get('/', function () {
    return view('welcome');    
});
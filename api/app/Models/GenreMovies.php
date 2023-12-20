<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Illuminate\Database\Eloquent\SoftDeletes;

class GenreMovies extends Model
{
    use SoftDeletes;

    protected $table = 'genre_movies';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

}
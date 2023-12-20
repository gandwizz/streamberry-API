<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use SoftDeletes;

    protected $table = 'movies';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'streaming_id',
        'genre_movie_id',
        'synopsis',
        'month_release',
        'year_release',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

}
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
        'genre_movie_id',
        'synopsis',
        'month_release',
        'year_release',
        'deleted_at',
        'created_at',
        'updated_at'
    ];


    public function streamings()
    {
        return $this->belongsToMany(Streaming::class, 'movies_streamings', 'movie_id', 'streaming_id');
    }
    

    public function genres()
    {
        return $this->hasOne(GenreMovies::class, 'id', 'genre_movie_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessments::class, 'movie_id');
    }

}
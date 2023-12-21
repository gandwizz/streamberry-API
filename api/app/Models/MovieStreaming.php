<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Illuminate\Database\Eloquent\SoftDeletes;

class MovieStreaming extends Model
{
    use SoftDeletes;

    protected $table = 'movies_streamings';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'streaming_id',
        'movie_id',
    ];


    public function streaming(){
        return $this->hasMany(Streaming::class, 'id', 'streaming_id');
    }
    

    public function genreMovies()
    {
        return $this->hasOne(GenreMovie::class, 'id', 'genre_movie_id');
    }
    

}
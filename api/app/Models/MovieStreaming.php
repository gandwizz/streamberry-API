<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovieStreaming extends Model
{
    use SoftDeletes;

    protected $table = 'movies_streamings';
    protected $guarded = ['id'];
    protected $fillable = [
        'streaming_id',
        'movie_id'
    ];

    // public function streaming()
    // {
    //     return $this->belongsToMany(Streaming::class, 'streaming_id', 'id');
    // }

    // public function movie()
    // {
    //     return $this->belongsToMany(Movie::class, 'movie_id', 'id');
    // }

    public function streamings()
    {
        return $this->hasMany(Streaming::class, 'id', 'streaming_id');
    }

    public function movies()
    {
        return $this->hasMany(Movie::class, 'id', 'movie_id');
    }
}

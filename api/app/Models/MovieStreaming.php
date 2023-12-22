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
        'name',
        'streaming_id',
        'movie_id',
    ];

    public function streaming()
    {
        return $this->belongsTo(Streaming::class, 'streaming_id', 'id');
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'id');
    }
}

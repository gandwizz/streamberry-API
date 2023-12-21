<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Illuminate\Database\Eloquent\SoftDeletes;

class Assessments extends Model
{
    use SoftDeletes;

    protected $table = 'assessments';
    protected $guarded = ['id'];
    protected $fillable = [
        'movie_id',
        'user_id',
        'streaming_id',
        'assessment',
        'comment',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }

    public function streaming()
    {
        return $this->belongsTo(Streaming::class, 'streaming_id');
    }

}
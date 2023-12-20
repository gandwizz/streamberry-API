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
        'assessment',
        'comment',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

}
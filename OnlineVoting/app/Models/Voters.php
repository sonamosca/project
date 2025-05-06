<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voters extends Model
{
    //
    protected $table = 'voters';

    protected $fillable = [
        'voter_id',
        'name',
        'gender',
        'role',
        'programme',
        'class',
        'email',
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// REMOVE: use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory; // REMOVE: , SoftDeletes

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
        // 'status',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class scope extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];
    /**
     * Defines the relationship: A Scope can have many Events.
     */
    public function events()
    {
        // Finds Event models where 'scope_id' matches this Scope's 'id'
        return $this->hasMany(Event::class, 'scope_id');
    }

}

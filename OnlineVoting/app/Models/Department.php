<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Add the 'name' field
    ];

    /**
     * Get the programmes associated with the department.
     * Each Department can have many Programmes.
     */
    public function programmes(): HasMany
    {
        // Assumes your Programme model is named 'Programme'
        // and the foreign key in the 'programmes' table is 'department_id'
        return $this->hasMany(Programme::class);
    }

    /**
     * Get the events specifically restricted to this department.
     * Each Department can have many Events restricted to it.
     */
    public function events(): HasMany
    {
        // Assumes your Event model is named 'Event'
        // and the foreign key in the 'events' table is 'department_id'
        return $this->hasMany(Event::class);
    }
}

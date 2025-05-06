<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programme extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'department_id',
    ];

    /**
     *  * Get the department that owns the programme.
     * Each Programme belongs to one Department.
     */
    public function department(): BelongsTo
    {
        // Assumes the foreign key in the 'programmes' table is 'department_id'
        // and it relates to the 'id' on the 'departments' table.
        return $this->belongsTo(Department::class);
    }
    /**
     * Get the voters associated with this programme.
     * Each Programme can have many Voters.
     */
    public function voters(): HasMany
    {
         // Assumes the foreign key in the 'voters' table is 'programme_id'
        return $this->hasMany(Voter::class, 'programme_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\VoteRecord;
use App\Models\Programme;
use App\Models\Department;
use App\Models\Candidate;

class Event extends Model
{
    use HasFactory; 

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',   
        'type',
        'gender_restriction',
        'programme_id',
        'department_id',
    ];
     /**
     * Get the department this event is restricted to (if any).
     */
    public function department(): BelongsTo
    {
        // Links to the Department model.
        // Uses withDefault to avoid errors if department_id is null.
        return $this->belongsTo(Department::class)->withDefault([
            'id' => null,
            'name' => '-- Not Department Specific --'
        ]);
    }
    /**
     * Get the programme associated with the event (if event is programme-specific).
     */
    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class)->withDefault([
            'id' => null, // Default attribute value
            'name' => '-- Not Programme Specific --' // Default attribute value
        ]);
    }

    /**
     * Get all of the vote records for the Event.
     */
    public function voteRecords(): HasMany
    {
        return $this->hasMany(VoteRecord::class, 'event_id');
    }

    /**
     * Get all of the candidates for the Event.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    protected $casts = [
        'event_date' => 'date',
    ];
}

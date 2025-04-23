<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteRecord extends Model
{
    use HasFactory;

    // Specify the table if necessary (usually not needed)
    // protected $table = 'vote_records';

    protected $fillable = [
        'event_id',
        'voter_id',
    ];

    /**
     * Get the event that this vote record belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the voter that this vote record belongs to.
     */
    public function voter()
    {
        return $this->belongsTo(Voter::class, 'voter_id');
    }
}

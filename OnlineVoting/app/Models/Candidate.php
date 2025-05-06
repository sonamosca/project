<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'voter_id', // This is the FK linking to voters.id
        'photo_path',
        // Add other fields here if you add them to migration
    ];

    /**
     * Get the event this candidate belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the voter record associated with this candidate.
     */
    public function voter()
    {
        // Links the 'voter_id' column in this table
        // to the 'id' primary key in the 'voters' table
        return $this->belongsTo(Voter::class);
    }
}

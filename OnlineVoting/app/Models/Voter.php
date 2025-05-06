<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Programme;
use App\Models\VoteRecord;
use App\Models\Event;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Candidate;

class Voter extends Model
{
    use HasFactory;

    // Specify the table if name differs drastically from model (usually not needed)
    // protected $table = 'voters';

    // Define which attributes can be mass-assigned
    protected $fillable = [
        'voter_id',
        'name',
        'gender',
        'programme',
        'email',
        'role',
        'programme_id',
        // Add other fillable fields defined in your migration
    ];

    /**
     * Get the programme that the voter belongs to.
     */
    public function programme(): BelongsTo {
        // Assumes foreign key in 'voters' table is 'programme_id'
        return $this->belongsTo(Programme::class, 'programme_id');
    }
    /**
     * Get the vote records associated with the voter.
     */
    public function voteRecords(): HasMany
    {
        // Assumes foreign key in vote_records is 'voter_id'
        return $this->hasMany(VoteRecord::class);
    }

    // You can also define the inverse relationship with Events through vote_records
    public function votedEvents()
    {
        return $this->belongsToMany(Event::class, 'vote_records', 'voter_id', 'event_id')->withTimestamps();
    }
    
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }
}

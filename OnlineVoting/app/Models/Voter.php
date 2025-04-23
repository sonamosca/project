<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    use HasFactory;

    // Specify the table if name differs drastically from model (usually not needed)
    // protected $table = 'voters';

    // Define which attributes can be mass-assigned
    protected $fillable = [
        'voterID',
        'name',
        'gender',
        'programme',
        'email',
        // Add other fillable fields defined in your migration
    ];

    /**
     * Get the vote records associated with the voter.
     */
    public function voteRecords()
    {
        // Assumes foreign key in vote_records is 'voter_id'
        return $this->hasMany(VoteRecord::class, 'voter_id');
    }

    // You can also define the inverse relationship with Events through vote_records
    public function votedEvents()
    {
        return $this->belongsToMany(Event::class, 'vote_records', 'voter_id', 'event_id')->withTimestamps();
    }   
}

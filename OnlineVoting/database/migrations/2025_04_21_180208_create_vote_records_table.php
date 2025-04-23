<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vote_records', function (Blueprint $table) {
            $table->id(); // Primary key for the record itself
            $table->foreignId('event_id')
                  ->constrained('events') // Links to 'id' column on 'events' table
                  ->onDelete('cascade'); // If event is deleted, delete vote records

            $table->foreignId('voter_id')
                  ->constrained('voters') // Links to 'id' column on 'voters' table
                  ->onDelete('cascade'); // If voter is deleted, delete vote records
                  
            $table->timestamps(); // recorded_at = created_at

            // Ensure a voter can only vote ONCE per event
            $table->unique(['event_id', 'voter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_records');
    }
};

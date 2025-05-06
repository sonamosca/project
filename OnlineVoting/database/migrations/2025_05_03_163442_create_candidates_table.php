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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            // Foreign key linking to the specific event
            $table->foreignId('event_id')
                  ->constrained('events') // Link to events table
                  ->cascadeOnDelete();

             // Foreign key linking to the voter who IS the candidate
            $table->foreignId('voter_id')
                  ->constrained('voters') // Link to voters table (voters.id primary key)
                  ->cascadeOnDelete();

            // Column to store the path to the candidate's photo
            $table->string('photo_path')->nullable(); // Path to image file
            $table->timestamps();
            $table->unique(['event_id', 'voter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};

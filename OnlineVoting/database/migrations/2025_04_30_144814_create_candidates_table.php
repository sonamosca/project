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
            $table->foreignId('voter_id')->constrained('users')->onDelete('cascade'); // Link to the user who is the candidate
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Link to the event they are running in
            $table->string('image_path')->nullable(); // Path to the candidate's picture
            // Add any other candidate-specific fields if needed (e.g., position)
            $table->timestamps();

             // Ensure a user can only be a candidate once per event
             $table->unique(['voter_id', 'event_id']);
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

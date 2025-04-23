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
        Schema::create('voters', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('voterID')->unique(); // The scannable/unique ID for the voter
            $table->string('name');
            $table->string('gender')->nullable(); // Optional?
            $table->string('programme')->nullable(); // Optional?
            $table->string('email')->unique()->nullable(); // Optional? Should it be unique?
            // Add any other columns specific to a voter
            // $table->string('department')->nullable();
            // $table->year('year_of_study')->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};

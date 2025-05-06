<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info("Dropping 'classes' table (if it exists).");
        Schema::dropIfExists('classes'); // Use the exact table name 'classes'
    }

    /**
     * Reverse the migrations.
     * Recreates the classes table for rollback purposes.
     */
    public function down(): void
    {
         Log::info("Recreating 'classes' table for rollback.");
         // If you need rollback, define how to recreate the table
         if (!Schema::hasTable('classes')) {
             Schema::create('classes', function (Blueprint $table) {
                 $table->id();
                 $table->string('name');
                 // Make sure programmes table exists if you add constraint
                 $table->foreignId('programme_id')->nullable()->constrained('programmes')->onDelete('set null');
                 $table->timestamps();
             });
        }
    }
};
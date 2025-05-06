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
        Schema::table('events', function (Blueprint $table) {
             // 1. Add class_id column
            $table->foreignId('class_id')
                  ->nullable() // <-- Allows NULL values
                  ->after('location') // <-- Place column after 'location' (adjust as needed)
                  ->constrained('classes') // <-- Creates foreign key constraint to 'classes' table
                  ->nullOnDelete(); // <-- If the referenced class is deleted, set this to NULL
                  
            // 2. Add scope_id column
            $table->foreignId('scope_id')
            ->after('class_id') // <-- Place column after 'class_id'
            // ->after('location') // <-- Corrected placement if class_id isn't always first
            ->default(1)
            ->constrained('scopes') // <-- Creates foreign key constraint to 'scopes' table
            ->restrictOnDelete(); // <-- IMPORTANT: Prevent deleting a scope if events still use it

            
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['scope_id']); // Drop FK for scope_id first
            $table->dropForeign(['class_id']); // Drop FK for class_id

            // Drop columns in reverse order of creation (or just specify names)
            $table->dropColumn('scope_id');
            $table->dropColumn('class_id');
        });
    }
};

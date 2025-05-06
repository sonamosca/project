<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- Add this use statement

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CORRECTED: Target the 'events' table
        Schema::table('events', function (Blueprint $table) {
            // Add the new department restriction column
            // Make sure 'programme_id' or another valid column exists in 'events' table
            $table->foreignId('department_id')
                  ->nullable()
                  ->after('programme_id') // <-- Make sure this column exists in 'events'! Change if needed.
                  ->constrained('departments')
                  ->onDelete('set null');

            // Remove the old class restriction column (if it exists on the 'events' table)
            if (Schema::hasColumn('events', 'class_id')) {
                 // Ensure we have access to DB facade if needed, already added `use DB` above
                 $foreignKeys = collect(DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?", [DB::getDatabaseName(), 'events', 'class_id']))->pluck('CONSTRAINT_NAME');

                 // Drop the foreign key FIRST (important!)
                 foreach ($foreignKeys as $key) {
                     // Check if the key name matches the convention or a known pattern
                     if (str_contains($key, '_class_id_foreign')) { // More generic check
                         $table->dropForeign($key);
                     }
                 }
                 // Then drop the column
                $table->dropColumn('class_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // CORRECTED: Target the 'events' table
        Schema::table('events', function (Blueprint $table) {

             // Add back the class_id column first (assuming it was nullable)
             // IMPORTANT: Make sure 'classes' is the correct table it originally referenced!
             if (!Schema::hasColumn('events', 'class_id')) {
                 // You might need to know which column it should go 'after'
                 // $table->foreignId('class_id')->nullable()->after('some_column')->constrained('classes')->onDelete('set null');
                 // Or just add it without 'after'
                  $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
             }


             // --- Now drop the department_id column and its constraint ---

             // Drop the department_id column (if it exists on the 'events' table)
             // NOTE: Dropping the column often drops the associated foreign key automatically in newer Laravel/DB versions.
             // But doing it explicitly is safer for broader compatibility.
            if (Schema::hasColumn('events', 'department_id')) {

                // Ensure we have access to DB facade, already added `use DB` above
                $foreignKeys = collect(DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?", [DB::getDatabaseName(), 'events', 'department_id']))->pluck('CONSTRAINT_NAME');

                 // Drop the foreign key FIRST
                 foreach ($foreignKeys as $key) {
                    if (str_contains($key, '_department_id_foreign')) { // More generic check
                        $table->dropForeign($key);
                    }
                }
                // Then drop the column
               $table->dropColumn('department_id');
           }


        });
    }
};
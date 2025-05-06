<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log; // Optional: for logging info

return new class extends Migration
{
    /**
     * Run the migrations.
     * Removes class_id from voters and events tables if they exist.
     */
    public function up(): void
    {
        // --- Modify 'voters' table ---
        if (Schema::hasTable('voters')) { // Check if table exists first
            Schema::table('voters', function (Blueprint $table) {
                // Check if the column exists before trying to drop
                if (Schema::hasColumn('voters', 'class_id')) {
                    Log::info("Dropping class_id foreign key and column from voters table.");
                    // Attempt to drop foreign key constraint first (names can vary)
                    try {
                        // Default convention: voters_class_id_foreign
                        // Adjust if your constraint name is different
                        $table->dropForeign(['class_id']);
                    } catch (\Exception $e) {
                        // Log if dropping default name fails, might not exist or name differs
                        Log::warning("Could not drop default foreign key 'voters_class_id_foreign'. It might not exist or have a different name. Attempting column drop anyway.");
                    }
                    // Drop the column itself
                    $table->dropColumn('class_id');
                } else {
                    Log::info("Column 'class_id' not found on 'voters' table. Skipping drop.");
                }

                 // Also drop 'class' string column if it exists based on previous discussion
                 if (Schema::hasColumn('voters', 'class')) {
                     Log::info("Dropping 'class' string column from voters table.");
                     $table->dropColumn('class');
                 } else {
                     Log::info("Column 'class' not found on 'voters' table. Skipping drop.");
                 }
            });
        } else {
            Log::info("Table 'voters' not found. Skipping modifications.");
        }


        // --- Modify 'events' table ---
        if (Schema::hasTable('events')) { // Check if table exists
             Schema::table('events', function (Blueprint $table) {
                // Check if the column exists before trying to drop
                if (Schema::hasColumn('events', 'class_id')) {
                    Log::info("Dropping class_id foreign key and column from events table.");
                    // Attempt to drop foreign key constraint first
                    try {
                        // Default convention: events_class_id_foreign
                        $table->dropForeign(['class_id']);
                    } catch (\Exception $e) {
                        Log::warning("Could not drop default foreign key 'events_class_id_foreign'. It might not exist or have a different name. Attempting column drop anyway.");
                    }
                    // Drop the column itself
                    $table->dropColumn('class_id');
                } else {
                     Log::info("Column 'class_id' not found on 'events' table. Skipping drop.");
                }
            });
        } else {
             Log::info("Table 'events' not found. Skipping modifications.");
        }

    }

    /**
     * Reverse the migrations.
     * Re-adds class_id to voters and events tables (nullable).
     * IMPORTANT: You would need the 'classes' table to exist for constrained() to work on rollback.
     */
    public function down(): void
    {
        // --- Modify 'voters' table ---
         if (Schema::hasTable('voters')) {
             Schema::table('voters', function (Blueprint $table) {
                if (!Schema::hasColumn('voters', 'class_id')) {
                     Log::info("Re-adding nullable class_id foreign key to voters table.");
                     // Note: This assumes 'classes' table exists on rollback
                     // Adjust 'after' column as needed
                     // $table->foreignId('class_id')->nullable()->after('programme_id')->constrained('classes')->onDelete('set null');
                }
                 if (!Schema::hasColumn('voters', 'class')) {
                     Log::info("Re-adding nullable 'class' string column to voters table.");
                    // $table->string('class')->nullable()->after('class_id');
                 }
             });
         }

        // --- Modify 'events' table ---
         if (Schema::hasTable('events')) {
             Schema::table('events', function (Blueprint $table) {
                 if (!Schema::hasColumn('events', 'class_id')) {
                     Log::info("Re-adding nullable class_id foreign key to events table.");
                    // Note: This assumes 'classes' table exists on rollback
                    // $table->foreignId('class_id')->nullable()->after('programme_id')->constrained('classes')->onDelete('set null');
                 }
             });
        }

        // Note: The 'down' method here is commented out by default as recreating
        // the exact previous state can be complex, especially regarding constraints
        // to a potentially dropped 'classes' table. Uncomment and adjust carefully
        // ONLY if you need robust rollback capabilities for this specific migration.
        Log::warning("Down migration for 'remove_class_id_foreign_keys' is largely placeholder. Manual reversal might be needed if rolling back past a 'drop_classes_table' migration.");

    }
};
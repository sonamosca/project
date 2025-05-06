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
            if (Schema::hasColumn('events', 'scope_id')) {
                // Drop foreign key first if exists (may need manual name check/raw SQL)
                 try { $table->dropForeign(['scope_id']); } catch (\Exception $e) { /* Ignore */ }
                $table->dropColumn('scope_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'scope_id')) {

                // 1. Re-add the column
                // Make sure the type and position match the original.
                // Add a default value if the table might have rows during rollback.
                // Assumes the 'all' scope ID is 1.
                $table->tinyInteger('scope_id')->unsigned()->default(1)->after('class_id'); // Adjust 'after' if needed

                // 2. Re-add the foreign key constraint
                // This assumes the 'scopes' table exists when rolling back.
                try {
                    $table->foreign('scope_id')
                          ->references('id')
                          ->on('scopes')
                          ->restrictOnDelete(); // Or cascade/set null depending on original constraint
                } catch (\Exception $e) {
                    // Log error if constraint can't be added (e.g., scopes table missing)
                    Log::warning("Could not re-add scope_id foreign key during rollback: " . $e->getMessage());
                }
           }
        });
    }
};

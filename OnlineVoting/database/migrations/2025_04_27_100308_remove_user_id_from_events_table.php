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
            if (Schema::hasColumn('events', 'user_id')) {
                // Drop foreign key first if it exists (check name in DB structure or previous migration)
                // Common convention: events_user_id_foreign
                // You might need to look up the exact constraint name in phpMyAdmin / Relation View
                try {
                     // Check if constraint exists before dropping
                     // Note: Checking constraint existence directly is tricky across DBs in plain Laravel schema builder
                     // It's often safer to just try dropping and potentially ignore the error if it doesn't exist,
                     // OR look up the name manually and use raw SQL if needed.
                     // For simplicity, let's assume Laravel's default naming convention.
                     $table->dropForeign(['user_id']); // Laravel tries to guess the name 'events_user_id_foreign'
                } catch (\Exception $e) {
                     // Log or ignore if the foreign key doesn't exist
                     Log::info("Could not drop user_id foreign key from events (maybe it didn't exist): " . $e->getMessage());
                }
                $table->dropColumn('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('location'); // Adjust position if needed
        // Re-add the constraint (ensure users table exists)
        $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};

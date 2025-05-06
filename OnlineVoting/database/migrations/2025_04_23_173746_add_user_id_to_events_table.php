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
            $table->foreignId('user_id')
                  ->nullable()         // Allows events without an owner (e.g., older ones)
                  ->after('id')        // Place it after the primary id column (optional)
                  ->constrained('users') // Sets up foreign key to users.id
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Laravel's default constraint name convention
            // Then drop the column
            $table->dropColumn('user_id');
        });
    }
};

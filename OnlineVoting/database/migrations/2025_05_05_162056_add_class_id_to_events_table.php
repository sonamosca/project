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
            $table->foreignId('class_id') // The foreign key column
                  ->nullable()           // Make it optional
                  ->after('programme_id') // Or suitable column
                  ->constrained('classes') // Link it to the 'classes' table (use correct table name)
                  ->onDelete('set null'); // If class is deleted, set event's class_id to NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['class_id']); // Drop FK first
            $table->dropColumn('class_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'type')) {
                $table->enum('type', ['all', 'students', 'staff']) // Use simplified options
                      ->default('all')
                      ->after('location') // Position after location (adjust if needed)
                      ->comment('Eligibility type (all, students, staff)');
            } else {
                // If it exists, ensure ENUM values are correct (MySQL specific)
                DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('all', 'students', 'staff') NOT NULL DEFAULT 'all' COMMENT 'Eligibility type'");
            }

            // --- Add 'gender_restriction' column ---
            if (!Schema::hasColumn('events', 'gender_restriction')) {
                $table->enum('gender_restriction', ['both', 'female'])
                      ->default('both')
                      ->after('type') // Position after type
                      ->comment('Gender restriction');
             } else {
                // Ensure ENUM values are correct (MySQL specific)
                 DB::statement("ALTER TABLE events MODIFY COLUMN gender_restriction ENUM('both', 'female') NOT NULL DEFAULT 'both' COMMENT 'Gender restriction'");
             }

            // --- Add 'programme_id' column ---
            if (!Schema::hasColumn('events', 'programme_id')) {
                $table->foreignId('programme_id')
                      ->nullable() // Can be NULL for 'All Programmes'
                      ->after('gender_restriction') // Position after gender_restriction
                      ->comment('Link to specific programme (null=all)')
                      ->constrained('programmes') // Link to programmes table
                      ->nullOnDelete(); // Set to NULL if programme is deleted
            } else {
                // If it exists, ensure it's nullable (unsignedBigInteger is assumed by foreignId)
                $table->unsignedBigInteger('programme_id')->nullable()->change();
            }

            // --- Ensure 'class_id' column is nullable ---
            // Assumes class_id was added previously as foreignId (UNSIGNED BIGINT)
            if (Schema::hasColumn('events', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->change();
                 // Add comment if desired
                 DB::statement("ALTER TABLE events MODIFY COLUMN class_id BIGINT UNSIGNED NULL COMMENT 'Link to specific class (null=not class specific)'");
            }
            // NOTE: We are NOT adding class_id here, just ensuring it's nullable if it exists.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'programme_id')) {
                // Attempt to drop foreign key first
                try { $table->dropForeign(['programme_id']); } catch (\Exception $e) { /* Ignore if constraint not found */ }
                $table->dropColumn('programme_id');
            }

            // Drop gender_restriction
            if (Schema::hasColumn('events', 'gender_restriction')) {
                $table->dropColumn('gender_restriction');
            }

            // Drop type
            if (Schema::hasColumn('events', 'type')) {
                $table->dropColumn('type');
            }

        });
    }
};

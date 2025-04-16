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
        Schema::create('students', function (Blueprint $table) {
            $table->id(); // Keeps the primary key

            // --- Add your specific student columns HERE ---
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('student_id_number', 50)->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('enrollment_date');
            $table->string('status', 50)->default('active');
            // --- End of specific student columns ---

            $table->timestamps(); // Keeps the created_at/updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

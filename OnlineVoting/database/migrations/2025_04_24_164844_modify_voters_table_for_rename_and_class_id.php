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
        Schema::table('voters', function (Blueprint $table) {
            // 1. Add the class_id column
            $table->foreignId('class_id')
                  ->nullable()
                  ->constrained('classes') // Add foreign key constraint
                  ->nullOnDelete();
            // 2. Add programme_id column 
            $table->foreignId('programme_id')
                  ->nullable()
                  ->constrained('programmes')
                  ->nullOnDelete();




        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
            $table->dropForeign(['programme_id']);
            $table->dropColumn('programme_id');
        });
    }
};

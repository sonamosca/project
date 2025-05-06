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
        Schema::create('voters', function (Blueprint $table) {
        
            // --- Add your specific student columns HERE ---
            $table->string('voter_id', 50)->unique()->nullable();
            $table->string('name');
            $table->string('gender');
            $table->string('role');
            $table->string('programme');
            $table->string('class');
            $table->string('email')->unique();
           
    
            
           
            // --- End of specific student columns ---

            $table->timestamps(); // Keeps the created_at/updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};

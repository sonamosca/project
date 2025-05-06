<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Scope;

class scopeseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Scope::firstOrCreate(
            ['name' => 'all'], // Check if a scope with name 'all' exists
            ['description' => 'Visible/Open to everyone'] // If not, create it with this description
        );

        Scope::firstOrCreate(
            ['name' => 'class-based'], // Check for 'class-based'
            ['description' => 'Visible/Open only to members of the specified class'] // Create if not found
        );

        Scope::firstOrCreate(
            ['name' => 'girls-only'], // Check for 'girls-only'
            ['description' => 'Visible/Open only to users identified as female/girl'] // Create if not found
        );

    }
}

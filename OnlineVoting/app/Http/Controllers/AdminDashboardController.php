<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller // Renamed to DashboardController
{
    public function index()
    {
        $eventCount = 0;
        $candidateCount = 0;
        $voterCount = 1; // From your example
        $resultsAvailableCount = 0;

        // This looks for 'resources/views/admin/dashboard.blade.php'
        return view('admin.dashboard', compact(
            'eventCount',
            'candidateCount',
            'voterCount',
            'resultsAvailableCount'
        ));
    }

    public function manageevent()
    {
        
        return view('admin.manage_event'); 
    }
    
}

<?php

// Ensure this namespace matches the file location
// If it's in app/Http/Controllers/Polling/, this is correct.
namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // Import base controller
use Illuminate\Http\Request;
use Illuminate\View\View; // Import View facade

class PollingDashboardController extends Controller
{
    /**
     * Display the polling officer's dashboard.
     * This method is called by the route named 'polling.dashboard'.
     */
    public function index(): View // Add return type hint
    {
        // Later, you might fetch data specific to the polling officer or active events here
        // $dataToPass = [ 'activeEventCount' => 5 ]; // Example
        // return view('polling.dashboard', $dataToPass);

        // For now, just return the basic view.
        // Make sure 'resources/views/polling/dashboard.blade.php' exists.
        return view('polling.dashboard');
    }
}
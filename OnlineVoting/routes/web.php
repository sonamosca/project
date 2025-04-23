<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminDashboardController as AdminDashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home',[HomeController::class,'redirect']);
// Route::get('/contact',[PageController::class,'redirect']);
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


// --- ADMIN ROUTES ---
Route::prefix('admin')
     ->name('admin.')
     // ->middleware(['auth', 'isAdmin']) // Add security middleware later!
     ->middleware(['auth']) // Start with just login required
     ->group(function () {

    // Your Admin Dashboard Route
 
    Route::get('/dashboard', [AdminDashboardController::class, 'index']) // Still uses alias, but alias now points to controller without \Admin in namespace
    ->name('dashboard');

    Route::get('/events', [EventController::class, 'index'])
    ->middleware(['auth']) // Protect the page
    ->name('events.index'); // Name for the page route

    Route::post('/events', [EventController::class, 'store'])
    ->middleware(['auth']) // Protect the action
    ->name('events.store'); // Name referenced in Blade/JS

    
    Route::get('/events/history', [EventController::class, 'showHistoryPage'])
    ->name('events.history_page');

    Route::delete('/events/{event}', [App\Http\Controllers\EventController::class, 'destroy'])
    ->name('events.destroy');

    Route::get('/events/{event}/edit', [EventController::class, 'edit'])
     ->name('events.edit');

    Route::put('/events/{event}', [EventController::class, 'update'])
     ->name('events.update');

    Route::get('/events/{event}/scan', [EventController::class, 'scanPage'])
         ->name('events.scan_page'); // admin.events.scan_page - Shows the scan interface for a specific event

    Route::post('/events/{event}/record-vote', [EventController::class, 'recordVote'])
         ->name('events.record_vote'); // admin.events.record_vote - Handles recording a vote via AJAX

    Route::get('/events/{event}/recorded-voters', [EventController::class, 'getRecordedVoters'])
         ->name('events.get_recorded_voters'); // admin.events.get_recorded_voters - Gets recorded voters via AJAX

    // ... Other admin routes go here later ...

});

// Route to SHOW the contact form page (GET request)
Route::get('/contact', [PageController::class, 'showContactForm'])->name('contact');

// Route to HANDLE the contact form SUBMISSION (POST request)
Route::post('/contact/submit', [PageController::class, 'submitContactForm'])->name('contact.submit');

// --- Add the Instructions Route HERE ---
Route::get('/instructions', [PageController::class, 'instructions'])->name('instructions');

// Route::get('/manage_event_view', [AdminDashboardController::class, 'manageevent']);

// Route::delete('/event-categories/{event}', [EventCategoryController::class, 'destroy'])
//     ->name('event-categories.destroy');




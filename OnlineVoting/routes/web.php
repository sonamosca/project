<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminDashboardController as AdminDashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ScopeController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VotersController;
use App\Http\Controllers\PollingDashboardController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Controllers\UserManagementController;

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


// --- SUPER ADMIN ROUTES ---
Route::prefix('admin')
     ->name('admin.')
     ->middleware(['auth', 'isAdmin'])
     ->group(function () {

    // Your Admin Dashboard Route
 
    Route::get('/dashboard', [AdminDashboardController::class, 'index']) // Still uses alias, but alias now points to controller without \Admin in namespace
    ->name('dashboard');
    Route::resource('users', UserManagementController::class)->except(['show']);

    Route::get('/events', [EventController::class, 'index'])
    ->name('events.index'); 

    Route::post('/events', [EventController::class, 'store'])
    ->name('events.store'); 
    
    Route::get('/events/history', [EventController::class, 'showHistoryPage'])
    ->name('events.history_page');

    Route::delete('/events/{event}', [App\Http\Controllers\EventController::class, 'destroy'])
    ->name('events.destroy');

    Route::get('/events/{event}/edit', [EventController::class, 'edit'])
     ->name('events.edit');

    Route::put('/events/{event}', [EventController::class, 'update'])
     ->name('events.update');

    Route::get('/events/{event}/scan', [EventController::class, 'scanPage'])
         ->name('events.scan_page'); 
    Route::post('/events/{event}/record-vote', [EventController::class, 'recordVote'])
         ->name('events.record_vote'); // admin.events.record_vote - Handles recording a vote via AJAX

    Route::get('/events/{event}/recorded-voters', [EventController::class, 'getRecordedVoters'])
         ->name('events.get_recorded_voters'); // admin.events.get_recorded_voters - Gets recorded voters via AJAX

    Route::get('/events/search', [EventController::class, 'search'])
         ->name('events.search');
    // --- Programme & Department Management ---
    Route::resource('programmes', ProgrammeController::class);
    Route::resource('departments', DepartmentController::class);
      // --- Candidate Overview & Deletion ---    
     Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
     Route::get('/candidates/select-event', [CandidateController::class, 'selectEvent'])->name('candidates.select_event');
     Route::get('/candidates/event/{event}/manage', [CandidateController::class, 'showManagePage'])->name('candidates.manage');
     Route::get('/candidates/find-voter', [CandidateController::class, 'findVoter'])->name('candidates.find_voter'); // AJAX
     Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');
     Route::delete('/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('candidates.destroy');
     Route::get('/candidates/view', [CandidateController::class, 'viewByEvent'])->name('candidates.view.index'); // Viewing existing candidates by event
     // Make sure this line exists inside the admin group
    Route::get('/candidates/{candidate}/edit', [CandidateController::class, 'edit'])->name('candidates.edit');
     // --- Voter Data Management ---
     Route::prefix('voters')->name('voters.')->group(function () {
          // Shows the page with BOTH the import form AND the manual add form
          Route::get('/manage', [VotersController::class, 'showImportForm'])->name('manage.index'); // Renamed for clarity

          // Handles the FILE IMPORT form submission
          Route::post('/import-file', [VotersController::class, 'importExcelData'])->name('import.file.store'); // More specific name

          // Handles the MANUAL ADD form submission
          Route::post('/add-manually', [VotersController::class, 'storeManual'])->name('store.manual');
      });
      
         // --- Scope Management (NEW) ---
    // This single line creates all the CRUD routes for scopes
    Route::resource('scopes', ScopeController::class); 

    // ... Other admin routes go here later ...

});

// ==============================================
// --- POLLING OFFICER Routes Group ---
// ==============================================
Route::prefix('polling-officer') 
    ->name('po.') 
    ->middleware(['auth', 'is_polling_officer'])
    ->group(function () {

        // This route's name will now be 'po.dashboard'
        Route::get('/dashboard', [PollingDashboardController::class, 'index'])->name('dashboard');

        // This route's name will now be 'po.selectEvent'
        Route::get('/select-event', [EventController::class, 'selectEventForPolling'])->name('selectEvent');

        // Consider renaming these for clarity as well, e.g., ->name('events.scan')
        Route::get('/event/{event}/scan', [EventController::class, 'scanPage'])->name('event.scan');
        Route::post('/event/{event}/record-vote', [EventController::class, 'recordVote'])->name('event.recordVote');
        Route::get('/event/{event}/recorded-voters', [EventController::class, 'getRecordedVoters'])->name('event.getRecordedVoters');

        Route::get('/candidates/view', [CandidateController::class, 'viewByEvent'])->name('candidates.view.index');
        Route::get('/candidates/event/{event}/manage', [CandidateController::class, 'showManagePage'])->name('candidates.manage');
        Route::get('/candidates/find-voter', [CandidateController::class, 'findVoter'])->name('candidates.find_voter');
        Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');
        Route::get('/candidates/{candidate}/edit', [CandidateController::class, 'edit'])->name('candidates.edit');
        Route::put('/candidates/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');

    });

// Route to SHOW the contact form page (GET request)
Route::get('/contact', [PageController::class, 'showContactForm'])->name('contact');

// Route to HANDLE the contact form SUBMISSION (POST request)
Route::post('/contact/submit', [PageController::class, 'submitContactForm'])->name('contact.submit');

// --- Add the Instructions Route HERE ---
Route::get('/instructions', [PageController::class, 'instructions'])->name('instructions');







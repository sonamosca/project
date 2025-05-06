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


use App\Http\Controllers\EventCategoryController;
use App\Http\Controllers\VotersController;
use App\Http\Controllers\Excel;

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
     ->middleware(['auth', 'isAdmin'])
     ->group(function () {

    // Your Admin Dashboard Route
 
    Route::get('/dashboard', [AdminDashboardController::class, 'index']) // Still uses alias, but alias now points to controller without \Admin in namespace
    ->name('dashboard');

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
    
    Route::resource('programmes', ProgrammeController::class);


    Route::resource('departments', DepartmentController::class);

     Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
     Route::get('/candidates/select-event', [CandidateController::class, 'selectEvent'])->name('candidates.select_event');
     Route::get('/candidates/event/{event}/manage', [CandidateController::class, 'showManagePage'])->name('candidates.manage');
     Route::get('/candidates/find-voter', [CandidateController::class, 'findVoter'])->name('candidates.find_voter'); // AJAX
     Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');
     Route::delete('/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('candidates.destroy');
         // --- Scope Management (NEW) ---
    // This single line creates all the CRUD routes for scopes
    Route::resource('scopes', ScopeController::class); 

    // ... Other admin routes go here later ...

});

// Route to SHOW the contact form page (GET request)
Route::get('/contact', [PageController::class, 'showContactForm'])->name('contact');

// Route to HANDLE the contact form SUBMISSION (POST request)
Route::post('/contact/submit', [PageController::class, 'submitContactForm'])->name('contact.submit');

// --- Add the Instructions Route HERE ---
Route::get('/instructions', [PageController::class, 'instructions'])->name('instructions');








Route::delete('/event-categories/{event}', [App\Http\Controllers\EventCategoryController::class, 'destroy'])
    ->name('event-categories.destroy');

    Route::get('voters/import', [VotersController::class, 'index'])->name('voters.index');
    Route::post('voters/import', [VotersController::class, 'importExcelData'])->name('voters.import');

    Route::post('voters/manual-add', [VotersController::class, 'manualAddVoter']);


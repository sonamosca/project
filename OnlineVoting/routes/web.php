<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminDashboardController as AdminDashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventCategoryController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home',[HomeController::class,'redirect']);
Route::get('/contact',[PageController::class,'redirect']);
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


    // ... Other admin routes go here later ...

});

// Route to SHOW the contact form page (GET request)
Route::get('/contact', [PageController::class, 'showContactForm'])->name('contact');

// Route to HANDLE the contact form SUBMISSION (POST request)
Route::post('/contact/submit', [PageController::class, 'submitContactForm'])->name('contact.submit');

// --- Add the Instructions Route HERE ---
Route::get('/instructions', [PageController::class, 'instructions'])->name('instructions');

Route::get('/manage_event_view', [AdminDashboardController::class, 'manageevent']);
Route::get('/admin/manage-events', [EventCategoryController::class, 'index'])
    ->middleware(['auth']) // Protect the page
    ->name('admin.manage_events.index'); // Name for the page route

// Route to handle the form submission (POST request from AJAX)
Route::post('/event-categories/store', [EventCategoryController::class, 'store'])
    ->middleware(['auth']) // Protect the action
    ->name('event-categories.store'); // Name referenced in Blade/JS


Route::get('/event-categories/history', [EventCategoryController::class, 'history'])
    ->middleware(['auth']) // <-- Add this for consistency
    ->name('event-categories.history');


// Route::delete('/event-categories/{event}', [EventCategoryController::class, 'destroy'])
//     ->name('event-categories.destroy');

Route::delete('/event-categories/{event}', [App\Http\Controllers\EventCategoryController::class, 'destroy'])
    ->name('event-categories.destroy');
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Event;
use App\Models\Candidate;
use App\Models\Voter;
use App\Models\Programme; 
use App\Models\Department; 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;  
use App\Http\Controllers\EventController; 
use Illuminate\Support\Facades\Storage; 

class CandidateController extends Controller
{
    public function index(): View
    {
        // No change needed
        return view('admin.candidates.index');
    }

    public function selectEvent(): View
    {
        // No change needed
        $events = Event::orderBy('event_date', 'desc')
                    ->orderBy('title', 'asc')
                    ->limit(100)
                    ->get(['id', 'title', 'event_date']);
        return view('admin.candidates.select_event', compact('events'));
    }

    public function showManagePage(Event $event): View
    {
        // *** MODIFIED: Load correct voter fields/relations ***
        $existingCandidates = Candidate::where('event_id', $event->id)
            ->with([
                // Select necessary fields including programme_id, remove 'class'
                'voter:id,voter_id,name,gender,email,role,programme_id',
                // Load nested names for display
                'voter.programme:id,name',
                'voter.programme.department:id,name'
            ])
            ->orderBy('id')
            ->get();

        return view('admin.candidates.manage', compact('event', 'existingCandidates'));
    }

    public function findVoter(Request $request): JsonResponse
    {
        // 1. Validate input - *** Expect event_id from JS ***
        $validated = $request->validate([
            'voter_id' => 'required|string|max:100|exists:voters,voter_id',
            // *** ADDED: Validation for event_id ***
            'event_id' => 'required|integer|exists:events,id'
        ], [
            'voter_id.*' => 'Voter ID not found or invalid.',
            'event_id.*' => 'Invalid event context provided.',
        ]);

        try {
            // 2. Find the Voter and Event
            $voter = Voter::where('voter_id', $validated['voter_id'])
                          ->with('programme.department') // Eager load relationships needed
                          // *** Select programme_id, removed class ***
                          ->first(['id', 'voter_id', 'name', 'gender', 'email', 'role', 'programme_id']);

            $event = Event::find($validated['event_id']);

            // Basic checks
            if (!$voter || !$event) {
                 Log::error("Voter or Event not found after validation.", $validated);
                 // Use the generic error message for consistency with screenshot
                 return response()->json(['success' => false, 'message' => 'An error occurred while searching for the voter.'], 500);
            }

            Log::info("Found voter {$voter->id} for potential candidacy in event {$event->id}. Performing checks.");

            // --- 3. Eligibility Checks ---
            $eventController = new EventController();
            if (!$eventController->checkVoterEligibility($voter, $event)) {
                Log::warning("Voter {$voter->id} not eligible for event {$event->id} (general restrictions).");
                return response()->json([
                    'success' => false,
                    // Provide specific feedback to user
                    'message' => 'Voter found, but is not eligible for this event based on Programme/Type/Gender restrictions.'
                ], 403);
            }

            if (strcasecmp($voter->role, 'student') != 0) { // Example: Only students can be candidates
                Log::warning("Voter {$voter->id} not eligible for candidacy (not a student) for event {$event->id}.");
                return response()->json([
                    'success' => false,
                    'message' => 'Only students can be added as candidates.'
                ], 403);
            }

            if (Candidate::where('voter_id', $voter->id)->where('event_id', $event->id)->exists()) {
                Log::warning("Voter {$voter->id} is already a candidate for event {$event->id}.");
                return response()->json([
                    'success' => false,
                    'message' => 'This voter is already a candidate for this event.'
                ], 409);
            }

            // --- 4. Prepare Success Response Data ---
            $voterData = [
                'id'             => $voter->id, // Primary Key for the form
                'voter_id'       => $voter->voter_id,
                'name'           => $voter->name,
                'gender'         => $voter->gender,
                'programme' => $voter->programme?->name ?? 'N/A',
                'department'=> $voter->programme?->department?->name ?? 'N/A',
                'email'          => $voter->email,
                'role'           => $voter->role,
            ];

            Log::info("Voter found and eligible for candidacy.", ['voter_id' => $voter->id, 'event_id' => $event->id]);
            return response()->json(['success' => true, 'voter' => $voterData]);

        } catch (\Illuminate\Validation\ValidationException $e) {
             Log::warning("Find voter validation failed.", ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            Log::error("Error finding/checking voter: " . $e->getMessage(), ['voter_id' => $request->input('voter_id'), 'event_id' => $request->input('event_id'), 'exception' => $e]);
            // Return generic error as seen in screenshot
            return response()->json(['success' => false, 'message' => 'An error occurred while searching for the voter.'], 500);
        }
    }

    public function store(Request $request): RedirectResponse
    {
        // *** Keep existing validation, expects voter PRIMARY KEY ID ***
        $validatedData = $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'voter_id' => 'required|integer|exists:voters,id', // This is the voter's PRIMARY KEY ID
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $eventId = $validatedData['event_id'];
        $voterId = $validatedData['voter_id']; // Voter's Primary Key

        // Re-check if already exists
        $alreadyExists = Candidate::where('event_id', $eventId)->where('voter_id', $voterId)->exists();
        if ($alreadyExists) {
            return Redirect::route('admin.candidates.manage', ['event' => $eventId])
                            ->with('error', 'This voter is already registered as a candidate.');
        }

        // Optional but Recommended: Final Eligibility Re-Check
        $voter = Voter::find($voterId);
        $event = Event::find($eventId);
        if ($voter && $event) {
            $eventController = new EventController();
             // Also check candidate-specific rules again (e.g., student role)
            if (!$eventController->checkVoterEligibility($voter, $event) || strcasecmp($voter->role, 'student') != 0) {
                 Log::warning("Attempt to store ineligible candidate.", ['voter_id' => $voterId, 'event_id' => $eventId]);
                 return Redirect::route('admin.candidates.manage', ['event' => $eventId])
                                 ->with('error', 'Voter is no longer eligible to be a candidate for this event.');
            }
        } else {
             Log::error("Voter or Event not found during final check.", ['voter_id' => $voterId, 'event_id' => $eventId]);
              return Redirect::route('admin.candidates.manage', ['event' => $eventId])
                                ->with('error', 'An error occurred finding voter/event details.');
        }

        // Handle File Upload
        $photoPath = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            try {
                $photoPath = $request->file('photo')->store('candidate_photos', 'public');
                if (!$photoPath) { throw new \Exception("Could not store photo."); }
                Log::info("Stored candidate photo.", ['path' => $photoPath]);
            } catch (\Exception $e) {
                Log::error("Photo Upload Error: " . $e->getMessage());
                return Redirect::route('admin.candidates.manage', ['event' => $eventId])->with('error', 'Could not upload candidate photo.');
            }
        }

        // Create the Candidate Record
        try {
            Candidate::create([
                'event_id' => $eventId,
                'voter_id' => $voterId, // Use primary key ID
                'photo_path' => $photoPath,
            ]);

            Log::info("Candidate added successfully.", ['event_id' => $eventId, 'voter_id' => $voterId]);
            // return Redirect::route('admin.candidates.manage', ['event' => $eventId])->with('success', 'Candidate added successfully!');
            return Redirect::route('admin.candidates.view.index', ['event_id' => $eventId])
                           ->with('success', 'Candidate added successfully!');

        } catch (QueryException $e) {
            Log::error("Error saving candidate: " . $e->getMessage());
            $errorMessage = 'Could not add candidate due to a database error.';
            if ($e->errorInfo[1] == 1062) { $errorMessage = 'This voter is already registered as a candidate.'; }
            return Redirect::route('admin.candidates.manage', ['event' => $eventId])->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error("Generic error saving candidate: " . $e->getMessage());
            return Redirect::route('admin.candidates.manage', ['event' => $eventId])->with('error', 'An unexpected error occurred.');
        }
    }


    /**
     * Remove the specified candidate resource from storage.
     */
     public function destroy(Candidate $candidate): RedirectResponse
     {
         $eventId = $candidate->event_id;
         Log::info("Attempting to delete candidate.", ['candidate_id' => $candidate->id, 'voter_id' => $candidate->voter_id, 'event_id' => $eventId]);
         try {
             $photoPath = $candidate->photo_path;
             if ($candidate->delete()) {
                 Log::info("Candidate record deleted successfully.");
                 if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                     if (Storage::disk('public')->delete($photoPath)) {
                          Log::info('Deleted candidate photo.', ['path' => $photoPath]);
                     } else {
                          Log::warning('Failed to delete candidate photo file.', ['path' => $photoPath]);
                     }
                 }
                 return Redirect::route('admin.candidates.manage', ['event' => $eventId])->with('success', 'Candidate removed successfully!');
             } else {
                 Log::warning("Candidate deletion failed.", ['candidate_id' => $candidate->id]);
                 return Redirect::route('admin.candidates.manage', ['event' => $eventId])->with('error', 'Could not remove candidate.');
             }
         } catch (\Exception $e) {
             Log::error("Error deleting candidate: " . $e->getMessage(), ['candidate_id' => $candidate->id]);
             return Redirect::route('admin.candidates.manage', ['event' => $eventId])->with('error', 'Could not remove candidate.');
         }
     }
     public function viewByEvent(Request $request): View
    {
        // Fetch all events for the dropdown, ordered by date or title
        $events = Event::orderBy('event_date', 'desc')->orderBy('title', 'asc')->get(['id', 'title']);

        $selectedEvent = null;
        $candidates = collect(); // Initialize as an empty collection

        // Check if an event_id is present in the request (i.e., an event has been selected)
        if ($request->has('event_id') && $request->event_id != '') {
            $selectedEventId = $request->input('event_id');
            $selectedEvent = Event::find($selectedEventId);

            if ($selectedEvent) {
                $candidates = Candidate::where('event_id', $selectedEventId)
                    ->with([
                        'voter:id,voter_id,name,gender,email,programme_id,role', // Added role back here
                        'voter.programme:id,name' // Assuming Voter has a 'programme' relationship
                                                 // and Programme model has a 'name' attribute.
                                                 // Adjust if your Voter model stores programme name directly.
                    ])
                    ->orderBy('id', 'asc') // Or order by voter name, etc.
                    ->get();
            }
        }

        return view('admin.candidates.view_by_event', compact('events', 'selectedEvent', 'candidates'));
    }
}
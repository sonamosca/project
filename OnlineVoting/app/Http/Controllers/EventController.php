<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Voter;
use App\Models\VoteRecord;
use App\Models\Programme;
// *** ADDED: Use statement for Department ***
use App\Models\Department;
// *** REMOVED: Use statement for RelatedClass ***
// use App\Models\RelatedClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * Display the event management page.
     */
    public function index(): View // Corrected return type hint
    {
        Log::debug("EventController@index accessed.");
        // *** CHANGED: Load department instead of relatedClass ***
        $events = Event::with(['programme', 'department'])->latest()->get();
        $programmes = Programme::orderBy('name')->get(['id', 'name']);
        // *** ADDED: Fetch Departments ***
        $departments = Department::orderBy('name')->get(['id', 'name']);
        // *** REMOVED: Fetching $classes ***
        // $classes = RelatedClass::orderBy('name')->get(['id', 'name']);
        // *** CHANGED: Pass departments, remove classes ***
        return view('admin.manage_event', compact('events', 'programmes', 'departments'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request): JsonResponse
    {
        Log::debug("EventController@store request received.", $request->all());
        $validator = Validator::make($request->all(), [
            'title' =>  'required|string|max:255|unique:events,title',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'type' => ['required', Rule::in(['all', 'students', 'staff'])],
            // *** CORRECTED: Added male option ***
            'gender_restriction' => ['required', Rule::in(['both', 'female', 'male'])],
            'programme_id' => 'nullable|exists:programmes,id',
            // *** ADDED: Validation for department_id ***
            // 'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($validator->fails()) {
            Log::warning("Event validation failed.", $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $validatedData = $validator->validated();
            $event = Event::create($validatedData);
            // *** CHANGED: Load department instead of relatedClass ***
            $event->load(['programme', 'department']);
            Log::info("Event created successfully.", ['event_id' => $event->id]);

            return response()->json([
                'message' => 'Event created successfully!',
                'event' => $event
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating event: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred while saving the event.'], 500);
        }
    }

    /**
     * Display the event history page.
     */
    public function showHistoryPage(): View
    {
        Log::debug("EventController@showHistoryPage accessed.");
        // *** CHANGED: Load department instead of relatedClass ***
        $historyEvents = Event::with(['programme', 'department'])
                            ->orderBy('created_at', 'desc')
                            ->get();
        return view('admin.events.history', compact('historyEvents'));
    }

    /**
     * PERMANENTLY remove the specified event from storage.
     */
    public function destroy(Event $event): JsonResponse
    {
        $eventId = $event->id;
        Log::info("Attempting to delete event.", ['event_id' => $eventId]);
        try {
            $isDeleted = $event->delete();
            if ($isDeleted) {
                Log::info("Event deleted successfully.", ['event_id' => $eventId]);
                return response()->json(['message' => 'Event deleted successfully!']);
            } else {
                Log::warning("Event deletion prevented by model event.", ['event_id' => $eventId]);
                return response()->json(['message' => 'Could not delete event (deletion prevented).'], 500);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error deleting event due to QueryException (ID: '.$eventId.'): '.$e->getMessage());
            if ($e->errorInfo[1] == 1451) {
                return response()->json(['message' => 'Could not delete event. It might still be referenced elsewhere.'], 409);
            }
            return response()->json(['message' => 'Could not delete event due to a database issue.'], 500);
        } catch (\Exception $e) {
            Log::error('Generic error deleting event (ID: '.$eventId.'): '.$e->getMessage());
            return response()->json(['message' => 'Could not delete event due to an unexpected error.'], 500);
        }
    }

    /**
     * Fetch data for editing a specific event.
     */
    public function edit(Event $event): JsonResponse
    {
        Log::debug("Fetching event for edit.", ['event_id' => $event->id]);
        // No changes needed, injected event has necessary IDs
        return response()->json(['event' => $event]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        Log::debug("EventController@update request received.", ['event_id' => $event->id, 'data' => $request->all()]);
        $validator = Validator::make($request->all(), [
            'title' => [
                'required', 'string', 'max:255',
                Rule::unique('events', 'title')->ignore($event->id)
            ],
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'type' => ['required', Rule::in(['all', 'students', 'staff'])],
             // *** CORRECTED: Added male option ***
            'gender_restriction' => ['required', Rule::in(['both', 'female', 'male'])],
            'programme_id' => 'nullable|exists:programmes,id',
             // *** ADDED: Validation for department_id ***
            // 'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($validator->fails()) {
            Log::warning("Event update validation failed.", ['event_id' => $event->id, 'errors' => $validator->errors()->toArray()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $validatedData = $validator->validated();
            $event->update($validatedData);
             // *** CHANGED: Load department instead of relatedClass ***
            $event->load(['programme', 'department']);
            Log::info("Event updated successfully.", ['event_id' => $event->id]);
            return response()->json([
                'message' => 'Event updated successfully!',
                'event' => $event
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating event (ID: '.$event->id.'): '.$e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the event.'], 500);
        }
    }

    /**
     * Display the voter scanning page for an event.
     */
    public function scanPage(Event $event): View
    {
        // No change needed
        Log::debug("Accessing scan page.", ['event_id' => $event->id]);
        return view('admin.events.scan', compact('event'));
    }

    /**
     * Record a vote for a specific voter in a specific event via AJAX.
     */
    public function recordVote(Request $request, Event $event): JsonResponse
    {
        // --- 1. Validation --- (No change needed)
        $validator = Validator::make($request->all(), [
             'voter_id' => 'required|string|max:100|exists:voters,voter_id',
         ], [ /* ... */ ]);
         if ($validator->fails()) { return response()->json([/*...*/], 422); }

        // --- 2. Find the Voter --- (No change needed)
        $scannedVoterIdentifier = $validator->validated()['voter_id'];
        $voter = Voter::where('voter_id', $scannedVoterIdentifier)->first();
        if (!$voter) { return response()->json([/*...*/], 404); }

        // --- 3. Check if Already Voted --- (No change needed)
        $alreadyVoted = VoteRecord::where('voter_id', $voter->id)->where('event_id', $event->id)->exists();
        if ($alreadyVoted) { return response()->json([/*...*/], 409); }

        // --- 4. CHECK ELIGIBILITY LOGIC --- (Calls the simplified function below)
        $isEligible = $this->checkVoterEligibility($voter, $event);
        if (!$isEligible) { return response()->json(['success' => false, 'message' => 'Error: Voter is not eligible for this specific event.'], 403); }

        // --- 5. Record the Vote --- (No change needed)
        try {
            VoteRecord::create([
                'event_id' => $event->id,
                'voter_id' => $voter->id,
            ]);
            Log::info("Vote recorded successfully: Voter {$voter->id} / Event {$event->id}");

            // --- 6. Return Success Response ---
            // *** CHANGED: Load programme/department for display, removed class ***
            $voter->loadMissing('programme.department');
            $voterData = [
                 'id' => $voter->id,
                 'voter_id' => $voter->voter_id,
                 'name' => $voter->name,
                 'gender' => $voter->gender,
                 'programme_name' => $voter->programme?->name ?? 'N/A', // Use loaded programme name
                 'department_name' => $voter->programme?->department?->name ?? 'N/A', // Use loaded dept name
                 'email' => $voter->email,
                 'role' => $voter->role,
                 // *** REMOVED: class_name ***
            ];
            return response()->json([
                'success' => true,
                'message' => 'Voter eligible and recorded successfully.',
                'voter'   => $voterData
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error recording vote for voter ID {$voter->id}, event ID {$event->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: Could not record vote due to a server error.'], 500);
        }
    }

    /**
     * Helper function to check voter eligibility (Simplified: No Class Check).
     */
    public function checkVoterEligibility(Voter $voter, Event $event): bool
    {
        // Event Restrictions
        $eventProgrammeId = $event->programme_id;
        $eventDepartmentId = $event->department_id;
        $eventType = $event->type;
        $eventGenderRestriction = $event->gender_restriction;

        // Voter Information
        $voterRole = $voter->role;
        $voterGender = $voter->gender;
        $voterProgrammeId = $voter->programme_id; // Direct from voter table

        // Load department only if needed for check OR logging
        $voterDepartmentId = null;
        if ($eventDepartmentId !== null || config('logging.log_voter_details')) { // Load if checking OR if detailed logging is enabled
             $voter->loadMissing('programme.department'); // Load the relationship chain
             $voterDepartmentId = $voter->programme?->department_id;
        }

        Log::debug("Check Start: Voter={$voter->id}, Event={$event->id}, EDep={$eventDepartmentId}, EProg={$eventProgrammeId}, EType={$eventType}, EGender={$eventGenderRestriction} | VoterDep={$voterDepartmentId}, VoterProg={$voterProgrammeId}, VoterRole={$voterRole}, VoterGender={$voterGender}");

        // --- Priority 1: Specific Department Restriction ---
        if ($eventDepartmentId !== null) {
            if ($voterDepartmentId != $eventDepartmentId) {
                Log::debug("Fail: Department mismatch. Required={$eventDepartmentId}, VoterDep={$voterDepartmentId}");
                return false;
            }
            Log::debug("Info: Department check passed or not required.");
        }

        // --- Priority 2: Specific Programme Restriction ---
        if ($eventProgrammeId !== null) {
            if ($voterProgrammeId != $eventProgrammeId) {
                Log::debug("Fail: Programme mismatch. Required={$eventProgrammeId}, VoterProg={$voterProgrammeId}");
                return false;
            }
            Log::debug("Info: Programme check passed or not required.");
        }

        // --- Priority 3: Type (Role) Check ---
        if ($eventType === 'students' && strcasecmp($voterRole, 'student') != 0) {
             Log::debug("Fail: Type 'students' required, Voter is '{$voterRole}'."); return false;
        }
        if ($eventType === 'staff' && strcasecmp($voterRole, 'staff') != 0) {
             Log::debug("Fail: Type 'staff' required, Voter is '{$voterRole}'."); return false;
        }
        Log::debug("Info: Type check passed.");

        // --- Priority 4: Gender Check ---
         // *** CORRECTED: Added male check ***
        if ($eventGenderRestriction === 'female' && strcasecmp($voterGender, 'female') != 0) {
            Log::debug("Fail: Gender 'female' required, Voter is '{$voterGender}'."); return false;
        }
        if ($eventGenderRestriction === 'male' && strcasecmp($voterGender, 'male') != 0) { // Make sure comparison value is consistent
            Log::debug("Fail: Gender 'male' required, Voter is '{$voterGender}'."); return false;
        }
        Log::debug("Info: Gender check passed.");

        Log::debug("Pass: All eligibility checks passed.");
        return true;
    }


    /**
     * Get list of voters already recorded for a specific event via AJAX.
     */
    public function getRecordedVoters(Event $event): JsonResponse
    {
         try {
             $voters = Voter::whereHas('voteRecords', function ($query) use ($event) {
                $query->where('event_id', $event->id);
             })
              // *** REMOVED: class_id from select ***
              // *** ADDED: programme_id if needed ***
             ->select('id', 'voter_id', 'name', 'gender', 'programme', 'email','role', 'programme_id') // Added programme_id
              // *** REMOVED: with('relatedClass') ***
              // Optional: Load programme/department if needed for display
             ->with('programme:id,name', 'programme.department:id,name') // Load names
             ->orderByDesc(
                 VoteRecord::select('created_at')
                     ->whereColumn('vote_records.voter_id', 'voters.id')
                     ->where('event_id', $event->id)
                     ->latest()
                     ->take(1)
             )
             ->get()
              // *** REMOVED: mapping class_name ***
              // Optional: map programme/department names if needed (already loaded)
             ->map(function ($voter) {
                 $voter->programme_name = $voter->programme?->name;
                 $voter->department_name = $voter->programme?->department?->name;
                 // unset($voter->programme); // Optional: remove nested objects if only names needed
                 return $voter;
             });

            return response()->json(['voters' => $voters]);

         } catch (\Exception $e) {
             Log::error("Get Recorded Voters Error: Event ID {$event->id} - " . $e->getMessage());
             return response()->json(['message' => 'Could not fetch recorded voters.'], 500);
         }
    }

    /**
     * Search for events based on query.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('query', ''); 
        // Start building the query, filtering by user
        $eventsQuery = Event::query();

        // If there is a search query, apply the multi-field search logic
        if (!empty($query)) {
            $eventsQuery->where(function ($q) use ($query) {
                $searchTerm = '%' . $query . '%';
                $q->where('title', 'LIKE', $searchTerm)
                  ->orWhere('location', 'LIKE', $searchTerm)
                  ->orWhere('event_date', 'LIKE', $searchTerm);
            });
        }

        // Execute the query and order results
        $events = $eventsQuery->with(['programme', 'department'])->latest()->get();
        return response()->json(['events' => $events]);
    }

}
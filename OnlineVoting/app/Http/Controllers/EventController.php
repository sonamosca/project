<?php

namespace App\Http\Controllers;

use App\Models\Event; // Use your Event model
use App\Models\Voter;
use App\Models\VoteRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * Display the event management page.
     */
    public function index()
    {
        $events = Event::latest()->get();
        return view('admin.manage_event', compact('events'));
    }

    /**
     * Store a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
        ]);

        // Return errors if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
        }

        // Create the event if validation passes
        try {
            $validatedData = $validator->validated();
            $event = Event::create($validatedData); // Use your Event model

            // Return success response with the created event data
            return response()->json([
                'message' => 'Event created successfully!',
                'event' => $event // Send the new event back to the frontend
            ], 201); // 201 Created

        } catch (\Exception $e) {
            Log::error('Error creating event: '.$e->getMessage()); // Log the error
            return response()->json(['message' => 'An error occurred while saving the event.'], 500); // 500 Internal Server Error
        }
    }

    public function showHistoryPage(): View 
    {
        $historyEvents = Event::orderBy('created_at', 'desc')->get();
        return view('admin.events.history', compact('historyEvents'));
    }

    /**
     * PERMANENTLY remove the specified event from storage.
     *
     * @param  \App\Models\Event  $event Automatically resolved by Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Event $event): JsonResponse
    {
        try {
            $eventId = $event->id; // Get ID before deleting event

            // ** ADD THIS LINE to delete associated vote records **
            VoteRecord::where('event_id', $eventId)->delete();

            // Now delete the event itself
            $event->delete(); // Or $event->forceDelete();
            $message = 'Event and associated vote records permanently deleted successfully!'; // Updated message

            return response()->json(['message' => $message]);

        } catch (\Exception $e) {
            Log::error('Error permanently deleting event (ID: '.$event->id.'): '.$e->getMessage());
            return response()->json(['message' => 'Could not delete event.'], 500);
        }
    }
        /**
     * Fetch data for editing a specific event.t
     *
     * @param  \App\Models\Event  $event  (Injected by Route Model Binding)
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Event $event): JsonResponse
    {
        return response()->json(['event' => $event]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        // Validation rules (similar to store, but 'required' might differ based on needs)
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
        ]);

        // Return errors if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the event if validation passes
        try {
            $validatedData = $validator->validated();
            // Update the existing $event model instance
            $event->update($validatedData);

            // Return success response with the updated event data
            return response()->json([
                'message' => 'Event updated successfully!',
                'event' => $event // Send the updated event back to the frontend
            ], 200); // 200 OK

        } catch (\Exception $e) {
            Log::error('Error updating event (ID: '.$event->id.'): '.$e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the event.'], 500);
        }
    }
    public function scanPage(Event $event): View
    {
        return view('admin.events.scan', compact('event'));
    }

    /**
     * Record a vote for a specific voter in a specific event via AJAX.
     */
    public function recordVote(Request $request, Event $event): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            // ** CHANGE: Use 'voterID' for validation **
            'voterID' => 'required|string|max:50|exists:voters,voterID', // <<< Use voterID here
        ], [
            // ** Adjust error messages **
            'voterID.required' => 'Voter ID is required.',
            'voterID.exists'   => 'Voter ID not found in the system.',
        ]);

        // ** CHANGE: Use 'voterID' key from request **
        if ($validator->fails()) {
             // Pass the specific field name that failed
             return response()->json(['status' => 'error', 'message' => $validator->errors()->first('voterID')], 422);
        }

        // ** CHANGE: Use 'voterID' key from validated data **
        $voterIdInput = $validator->validated()['voterID']; // <<< Use voterID here

        // 1. Find the Voter
        // ** CHANGE: Use Voter model and 'voterID' column **
        $voter = Voter::where('voterID', $voterIdInput)->first(); // <<< Use voterID here

        if (!$voter) {
            Log::warning("Voter ID {$voterIdInput} passed validation but not found for Event ID {$event->id}.");
            return response()->json(['status' => 'error', 'message' => 'Voter ID not found.'], 404);
        }

        // 2. Check if already voted in THIS event
        $alreadyVoted = VoteRecord::where('event_id', $event->id)
                                  ->where('voter_id', $voter->id) // Still uses voter->id (primary key) for relation
                                  ->exists();

        if ($alreadyVoted) {
            return response()->json([
                'status' => 'info',
                'message' => 'Already recorded for this event.',
                // ** CHANGE: Include 'voterID' in returned fields, adjust others **
                'voter' => $voter->only(['id', 'voterID', 'name', 'gender', 'programme', 'email']) // <<< Include voterID
            ], 200);
        }

        // 3. Record the vote
        try {
            VoteRecord::create([
                'event_id' => $event->id,
                'voter_id' => $voter->id, // Still uses primary key 'id' for relation
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Vote recorded successfully!',
                 // ** CHANGE: Include 'voterID' in returned fields, adjust others **
                'voter' => $voter->only(['id', 'voterID', 'name', 'gender', 'programme', 'email']) // <<< Include voterID
            ], 201);

        } catch (\Exception $e) {
            Log::error("Record Vote Error: Event ID {$event->id}, Voter ID {$voterIdInput} - " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server error recording vote.'], 500);
        }
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
             // ** CHANGE: Select 'voterID' column, adjust others **
             ->select('id', 'voterID', 'name', 'gender', 'programme', 'email') // <<< Select voterID
             ->orderByDesc(
                 VoteRecord::select('created_at')
                     ->whereColumn('vote_records.voter_id', 'voters.id')
                     ->where('event_id', $event->id)
                     ->latest()
                     ->take(1)
             )
             ->get();

            return response()->json(['voters' => $voters]);

         } catch (\Exception $e) {
             Log::error("Get Recorded Voters Error: Event ID {$event->id} - " . $e->getMessage());
             return response()->json(['message' => 'Could not fetch recorded voters.'], 500);
         }
    }

}
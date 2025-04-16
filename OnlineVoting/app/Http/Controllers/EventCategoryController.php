<?php

namespace App\Http\Controllers;

use App\Models\Event; // Use your Event model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EventCategoryController extends Controller
{
    /**
     * Display the event management page.
     */
    public function index()
    {
        $events = Event::all();
        // >> WHAT DOES THIS LOG LINE OUTPUT? <<
        Log::info('Events fetched for index page:', $events->toArray());
        return view('admin.manage_event', compact('events'));
    }

    /**
     * Store a newly created event category in storage.
     */
    public function store(Request $request)
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
                'eventCategory' => $event // Send the new event back to the frontend
            ], 201); // 201 Created

        } catch (\Exception $e) {
            Log::error('Error creating event: '.$e->getMessage()); // Log the error
            return response()->json(['message' => 'An error occurred while saving the event.'], 500); // 500 Internal Server Error
        }
    }

    public function history() // Method name kept for consistency with frontend, but logic changed
    {
        try {
            // Fetch ALL current non-deleted events (same as index)
            // Or implement different logic, e.g., fetch events with past dates
            $historyEvents = Event::latest()->get();

            // Example: Fetch events whose date is in the past
            // $historyEvents = Event::where('event_date', '<', now()->toDateString())
            //                      ->latest('event_date')
            //                      ->get();

            return response()->json(['historyEvents' => $historyEvents]); // Return current events

        } catch (\Exception $e) {
            Log::error('Error fetching event data for history view: '.$e->getMessage());
            // Adjusted error message slightly
            return response()->json(['message' => 'Could not load event data.'], 500);
        }
    }

    /**
     * PERMANENTLY remove the specified event from storage.
     *
     * @param  \App\Models\Event  $event Automatically resolved by Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Event $event) // Handles the DELETE request
    {
        try {
            // ** Use forceDelete() to permanently delete, even if SoftDeletes trait IS used **
            // $event->forceDelete();

            // ** Or use regular delete() if SoftDeletes trait is NOT used on the model **
            $event->delete(); // This will permanently delete if SoftDeletes trait is removed from the Event model

            return response()->json(['message' => 'Event permanently deleted successfully!']);

        } catch (\Exception $e) {
            Log::error('Error permanently deleting event (ID: '.$event->id.'): '.$e->getMessage());
            return response()->json(['message' => 'Could not delete event.'], 500);
        }
    }
}
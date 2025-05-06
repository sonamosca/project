<?php

namespace App\Http\Controllers;

use App\Models\Programme;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;


class ProgrammeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programmes = Programme::orderBy('name', 'asc')->paginate(15);
        return view('admin.programmes.index', compact('programmes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): view
    {
        $departments = Department::orderBy('name')->get(['id', 'name']); // Get only id and name
         // Pass the departments to the view
         return view('admin.programmes.create', compact('departments')); // <-- Pass departments
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:programmes,name', // Name must be unique in programmes table
            'department_id' => 'required|exists:departments,id', // Department is required and must exist
        ]);
        Programme::create($validatedData);
        return redirect()->route('admin.programmes.index')
                     ->with('success', 'Programme created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Programme $programme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Programme $programme): view 
    {
        $departments = Department::orderBy('name')->get(['id', 'name']); // Fetch departments
        return view('admin.programmes.edit', compact('programme', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Programme $programme)
    {
         // Validate input
         $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('programmes')->ignore($programme->id)],
            'department_id' => 'required|exists:departments,id', // Keep or add this rule
        ]);
        // Update the programme record
        $programme->update($validatedData);

        // Redirect back to index with success message
        return redirect()->route('admin.programmes.index')
                        ->with('success', 'Programme updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Programme $programme)
    {
        $programmeId = $programme->id; // Get ID for logging
        $programmeName = $programme->name; // Get name for messages

        Log::info("Attempting to delete programme.", ['programme_id' => $programmeId, 'name' => $programmeName]);

        try {
            // Attempt to delete the programme
            // NOTE: This will FAIL if any 'classes' records still have this programme_id
            // UNLESS you set onDelete('cascade') or onDelete('set null') on the
            // programme_id foreign key in the classes table migration.
            // Assuming you used restrict or no action (default), it will throw QueryException.
            $isDeleted = $programme->delete();

            if ($isDeleted) {
                Log::info("Programme deleted successfully.", ['programme_id' => $programmeId]);
                return redirect()->route('admin.programmes.index')
                                ->with('success', "Programme '{$programmeName}' deleted successfully.");
            } else {
                // Should not usually happen unless a 'deleting' model event returns false
                Log::warning("Programme deletion prevented by model event.", ['programme_id' => $programmeId]);
                return redirect()->route('admin.programmes.index')
                                ->with('error', "Could not delete programme '{$programmeName}' (deletion prevented).");
            }

        } catch (QueryException $e) {
            // Check if the error is specifically a foreign key constraint violation (MySQL code 1451)
            // This means related classes still exist.
            if ($e->errorInfo[1] == 1451) {
                Log::warning("Cannot delete programme because it's linked to classes.", ['programme_id' => $programmeId]);
                return redirect()->route('admin.programmes.index')
                                ->with('error', "Cannot delete programme '{$programmeName}'. It is still assigned to one or more classes. Please reassign or delete those classes first.");
            } else {
                // Handle other potential database errors
                Log::error('Error deleting programme due to QueryException (ID: '.$programmeId.'): '.$e->getMessage());
                return redirect()->route('admin.programmes.index')
                                ->with('error', "Could not delete programme '{$programmeName}' due to a database error.");
            }
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            Log::error('Generic error deleting programme (ID: '.$programmeId.'): '.$e->getMessage());
            return redirect()->route('admin.programmes.index')
                            ->with('error', "Could not delete programme '{$programmeName}' due to an unexpected error.");
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\RelatedClass;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse; 
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; 


class RelatedClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): view
    {
        $classes = RelatedClass::with('programme')
                              ->orderBy('name', 'asc')
                              ->paginate(15);

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): view
    {
        $programmes = Programme::orderBy('name')->get(['id', 'name']);
        return view('admin.classes.create', compact('programmes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Name must be unique within the classes table
            'name' => 'required|string|max:255|unique:classes,name',
            // programme_id is required and must exist in the programmes table
            'programme_id' => 'required|exists:programmes,id',
        ]);
    
        // Create new class (Ensure RelatedClass model has name and programme_id in $fillable)
        RelatedClass::create($validatedData);
    
        // Redirect back to index with success message
        return Redirect::route('admin.classes.index') // Use Redirect Facade
                       ->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RelatedClass $relatedClass)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RelatedClass $relatedClass): view
    {
        $programmes = Programme::orderBy('name')->get(['id', 'name']);
        return view('admin.classes.edit', compact('relatedClass', 'programmes'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RelatedClass $relatedClass)
    {
            // Validate input
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Check uniqueness BUT ignore the current class's own ID
                Rule::unique('classes', 'name')->ignore($relatedClass->id),
            ],
            'programme_id' => 'required|exists:programmes,id',
        ]);

        // Update the class record
        $relatedClass->update($validatedData);

        // Redirect back to index with success message
        return Redirect::route('admin.classes.index') // Use Redirect Facade
                    ->with('success', 'Class updated successfully.');
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RelatedClass $relatedClass)
    {
        $classId = $relatedClass->id;
        $className = $relatedClass->name;

        Log::info("Attempting to delete class.", ['class_id' => $classId, 'name' => $className]);

        try {
            // Attempt to delete the class.
            // This will likely FAIL if Voters or Events are linked via class_id
            // and the foreign keys use the default RESTRICT constraint.
            $isDeleted = $relatedClass->delete();

            if ($isDeleted) {
                Log::info("Class deleted successfully.", ['class_id' => $classId]);
                return Redirect::route('admin.classes.index')
                                ->with('success', "Class '{$className}' deleted successfully.");
            } else {
                Log::warning("Class deletion prevented by model event.", ['class_id' => $classId]);
                return Redirect::route('admin.classes.index')
                                ->with('error', "Could not delete class '{$className}' (deletion prevented).");
            }

        } catch (QueryException $e) {
            // Check for Foreign Key constraint violation (MySQL code 1451)
            if ($e->errorInfo[1] == 1451) {
                Log::warning("Cannot delete class because it is linked to voters or events.", ['class_id' => $classId]);
                // Provide a more specific message if possible (check error message details)
                // $errorMessage = $e->getMessage();
                // if (str_contains($errorMessage, 'fk_voters_class_id')) ... // Example check
                return Redirect::route('admin.classes.index')
                                ->with('error', "Cannot delete class '{$className}'. It is still assigned to voters or events. Please reassign or delete them first.");
            } else {
                // Handle other database errors
                Log::error('Error deleting class due to QueryException (ID: '.$classId.'): '.$e->getMessage());
                return Redirect::route('admin.classes.index')
                                ->with('error', "Could not delete class '{$className}' due to a database error.");
            }
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            Log::error('Generic error deleting class (ID: '.$classId.'): '.$e->getMessage());
            return Redirect::route('admin.classes.index')
                            ->with('error', "Could not delete class '{$className}' due to an unexpected error.");
        }
    }
}

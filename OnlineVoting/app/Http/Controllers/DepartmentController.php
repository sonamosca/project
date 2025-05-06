<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::orderBy('name', 'asc')->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): view
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);
        Department::create($validatedData);

        // Adjust route name if necessary (e.g., 'admin.departments.index' or just 'departments.index')
        // Let's assume for now your routes are still named like 'admin.departments.*'
        return redirect()->route('admin.departments.index')
               ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department): view
    {
        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department): view
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);
        $department->update($validatedData);

         // Let's assume for now your routes are still named like 'admin.departments.*'
        return redirect()->route('admin.departments.index')
               ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        try {
            $department->delete();
             // Let's assume for now your routes are still named like 'admin.departments.*'
             return redirect()->route('admin.departments.index')
                   ->with('success', 'Department deleted successfully.');

        } catch (\Exception $e) {
            // Log error: Log::error("Department Delete Error: " . $e->getMessage());
            return redirect()->route('admin.departments.index')
                   ->with('error', 'Failed to delete department. It might be in use.');
        }
    }
}

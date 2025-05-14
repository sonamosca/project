<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id()) // Ensure this line is updated
                    ->latest()
                    ->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Define the roles that can be assigned.
        // The keys are the values stored in the DB, values are display names.
        $roles = [
            // User::ROLE_ADMIN => 'Administrator',
            User::ROLE_POLLING_OFFICER => 'Polling Officer',
            // Add User::ROLE_VOTER => 'Voter' if you were to create voter accounts this way
        ];
        return view('admin.users.create', compact('roles')); // We'll create this view soon
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'], // Adjust validation as needed
            'address' => ['nullable', 'string', 'max:255'], // Adjust validation as needed
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' checks password_confirmation
            'role' => ['required', 'string', Rule::in([User::ROLE_POLLING_OFFICER])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = [
            // User::ROLE_ADMIN => 'Administrator',
            User::ROLE_POLLING_OFFICER => 'Polling Officer',
        ];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::in([User::ROLE_POLLING_OFFICER])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Password is optional on update
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
        ];

        if ($request->filled('password')) { // Only update password if a new one is provided
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
             return redirect()->route('admin.users.index')->with('error', 'Administrator accounts cannot be deleted through this interface.');
        }

        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }

         $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}

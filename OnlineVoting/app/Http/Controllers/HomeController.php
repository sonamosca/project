<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Keep this
use Illuminate\Support\Facades\Auth; // Keep this
use Illuminate\Http\RedirectResponse; // Add this for return type hint

class HomeController extends Controller
{
    /**
     * Redirect authenticated users to their appropriate dashboard.
     * This method is typically called after login.
     */
    public function redirect(Request $request): RedirectResponse // Add return type hint
    {
        // Check if a user is logged in
        if (Auth::check()) {
            $user = Auth::user();

            // Use the isAdmin() method from the User model (checks the 'is_admin' column)
            if ($user->isAdmin()) {
                // If user IS an admin (is_admin == 1), redirect to the ADMIN dashboard route
                return redirect()->route('admin.dashboard');
            } else {
                // If user IS NOT an admin (is_admin == 0), redirect to the POLLING dashboard route
                return redirect()->route('polling.dashboard');
            }
        }

        // If somehow accessed when not logged in, redirect to login page
        return redirect('/login');
    }

    /**
     * Optional: Handle direct access to /home if needed
     * (Often the 'redirect' method is sufficient if Fortify redirects to /home)
     */
    public function index()
    {
        // You can reuse the redirect logic here too
        return $this->redirect(request()); // Pass the current request
    }
}
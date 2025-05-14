<?php

namespace App\Http\Responses; // Correct namespace for the new file

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth; // To get the authenticated user

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = Auth::user(); // Get the currently authenticated user

        if ($user->isAdmin()) { // Uses User::isAdmin() which checks the 'role' column
            // Redirect admins to the admin dashboard route
            // We'll define 'admin.dashboard' later.
            // Using intended() tries to send them where they were going, or defaults here.
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->isPollingOfficer()) { // Uses User::isPollingOfficer()
            // Redirect polling officers to their dashboard route
            // We'll define 'po.dashboard' later.
            return redirect()->intended(route('po.dashboard'));
        }

        // Fallback for any other user type or if roles aren't matched.
        // This usually redirects to the 'home' route defined in config/fortify.php.
        return redirect()->intended(config('fortify.home'));
    }
}
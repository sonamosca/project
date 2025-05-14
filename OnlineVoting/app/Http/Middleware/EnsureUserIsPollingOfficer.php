<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPollingOfficer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access if the user is authenticated AND
        // EITHER they are a Polling Officer OR they are an Admin.
        // Admins often need to access sections managed by sub-roles for oversight or assistance.
        if (Auth::check() && (Auth::user()->isPollingOfficer() || Auth::user()->isAdmin())) {
            return $next($request);
        }

        // If the conditions are not met, abort with a 403 Forbidden error.
        abort(403, 'Access Denied. Polling Officer privileges required.');
    }
}
<?php // <-- Make sure this PHP opening tag is at the top of the file

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Add this line to import the Auth facade
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is logged in (authenticated)
        // 2. If logged in, check if their 'is_admin' database column is true (or 1)
        if (Auth::check() && Auth::user()->is_admin) {
            // If BOTH conditions are true, let the request continue to the controller
            return $next($request);
        }

        // If the user is NOT logged in, OR they are logged in but NOT an admin,
        // stop the request here and show a "Forbidden" error page.
        abort(403, 'Access Denied. Administrator privileges required.');
    }
}
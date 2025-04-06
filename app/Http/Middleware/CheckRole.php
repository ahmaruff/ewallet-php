<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get the user's role
        $roleList = Auth::user()->roles->pluck('name')->toArray(); 

        // Define the allowed routes for each role
        $rolePermissions = [
            'admin' => [
                '/',
                '/*',
                'admin',
                'admin/*',
                'dashboard',
                'dashboard/*',
                'user',
                'user/*',
            ],
            'user' => [
                'dashboard',
                'profile',
                'user',
                'user/*'
            ],
        ];

        // Check if any of the user's roles are in the provided roles
        $roleMatch = false;
        foreach ($roles as $role) {
            if (in_array($role, $roleList)) {
                $roleMatch = true;
                break;
            }
        }

        if (!$roleMatch) {
            return redirect()->route('dashboard');  // Redirect if role permissions are not defined
        }

        // Check if the requested route is allowed for the user's role
        foreach ($rolePermissions as $role => $allowedRoutes) {
            if (in_array($role, $roleList)) {
                foreach ($allowedRoutes as $allowedRoute) {
                    if ($request->is($allowedRoute)) {
                        return $next($request);  // Allow access if route matches
                    }
                }
            }
        }

        // If no match found, redirect the user to the appropriate page
        return redirect()->route('dashboard');
    }
}

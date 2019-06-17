<?php

namespace App\Http\Middleware;

use Closure;
use App\Outlet;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = auth()->user();

            // If Admin
            if ($user->is_admin) {
                return redirect(route('dashboard'));
            }

            // If Owner
            $outlet = $user->outlets()->first();
            if (!$outlet) {

                // If Teller
                $defaultOutlet = $user->defaultOutlet()->first();
                $outlet = Outlet::find($defaultOutlet->outlet_id);
            }

            if ($outlet) {
                return redirect(route('outlet-dashboard', ['outlet_id' => $outlet->id]));
            }
        }

        return $next($request);
    }
}

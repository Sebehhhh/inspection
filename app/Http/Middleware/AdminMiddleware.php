<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspectController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_admin) 
        {
            return $next($request);
        }

        // Izinkan user biasa hanya ke DashboardController dan InspectionController
        if ($request->route()->getControllerClass() === DashboardController::class ||
            $request->route()->getControllerClass() === InspectController::class) {
            return $next($request);
        }

        // Jika bukan admin dan mencoba mengakses selain dashboard & inspection, redirect ke dashboard
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
   
    }
}

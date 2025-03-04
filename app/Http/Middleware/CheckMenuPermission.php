<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if(!request()->user() ||  !$request->user()->can("access {$permission} menu")){
            return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access this menu');
        }
        return $next($request);
    }
}

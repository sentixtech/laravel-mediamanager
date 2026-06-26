<?php

namespace Sentix\MediaManager\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AssignGuard
{
    public function handle($request, Closure $next)
    {
        $guards = config('media.routes.middleware');
        //dd($guards);
        $activeGuard = collect($guards)->first(fn($g) => Auth::guard($g)->check());
        if (!$activeGuard) {
                return response()->json([
                    'error' => 'Unauthorized, please login first.'
                ], 401);
        }
        Auth::shouldUse($activeGuard);
        return $next($request);
    }
}
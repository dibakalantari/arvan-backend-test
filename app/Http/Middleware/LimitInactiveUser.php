<?php

namespace App\Http\Middleware;

use Closure;

class LimitInactiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth()->user() && auth()->user()->isInactive()) {
            return response()->json([
                'errors' => [
                    'message' => 'You are not allowed to store anything',
                    'status_code' => 403
                ]
            ], 403);
        }

        return $next($request);
    }
}

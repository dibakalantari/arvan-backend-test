<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfUserIsAdmin
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
        if(auth()->user()->is_admin)
        {
            return $next($request);
        }

        return response()->json([
            'errors' => [
                'message' => 'You dont have access to this part',
                'status_code' => 403
            ]
        ], 403);
    }
}

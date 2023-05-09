<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use JWTAuth;
use Closure;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {

        try {
            JWTAuth::parseToken()->authenticate(); // VALIDATE THE TOKEN
            if ($request->user()) {
                $response = $next($request);
                return $response;
            }

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            Log::error($e);
        }

        return response()->json([
            'message' => "error",
            'metadata' => [
                'pagination' => null
            ],
            'errors' => [
                'code' => 401,
                'message' => 'You are not authorized to access this resource.'
            ],
            'data' => null
        ], 401);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }

        return null;
    }
}

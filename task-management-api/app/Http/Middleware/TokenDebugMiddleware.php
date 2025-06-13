<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TokenDebugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log the authorization header for debugging
        $authHeader = $request->header('Authorization');
        Log::info('Auth header: ' . ($authHeader ?? 'none'));
        
        if ($authHeader) {
            // Extract token from Bearer format
            $token = str_replace('Bearer ', '', $authHeader);
            Log::info('Extracted token: ' . $token);
            
            // Clean token (remove any pipe characters or whitespace)
            $cleanToken = preg_replace('/[|\s]/', '', $token);
            if ($token !== $cleanToken) {
                Log::warning('Token needed cleaning. Original: ' . $token . ', Cleaned: ' . $cleanToken);
                
                // Replace the original token with the cleaned one
                $request->headers->set('Authorization', 'Bearer ' . $cleanToken);
                Log::info('Updated Authorization header: Bearer ' . $cleanToken);
            }
        }
        
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\api_token;

class auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken(); //cheak if token is present in the header

        if (!$token) { //In case the token is missing in the header
            return response()->json(['error' => 'Token missing'], 401);
        }

        $tokenRecord = api_token::where('token', $token)->first();

        if (!$tokenRecord) { //In case the token does not exist in our database
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // attach user to request
        $request->merge(['auth_user' => $tokenRecord->user]);

        return $next($request);
    
    }
}

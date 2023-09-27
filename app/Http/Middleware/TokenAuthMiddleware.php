<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $user = User::where('remember_token', $token)->first();

        if (!$user || !$token) return response()->json([
            'success' => false,
            'message' => 'un-authorized entry, invalid token'
        ], 401);

        return $next($request);
    }
}

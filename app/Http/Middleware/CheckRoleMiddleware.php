<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if(!$user){
            return response()->json([
                'status' => "error",
                'message' => 'Unauthenticated'
            ], 401);
        }
        if(in_array($user->role, $roles)) return $next($request);

        return response()->json([
            "status" => "error",
            "message" => "Forbidden. Akses ditolak karena peran Anda tidak memiliki izin untuk sumber daya ini."
        ], 403);
    }
}

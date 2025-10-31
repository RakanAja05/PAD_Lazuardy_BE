<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
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
                'code' => 'UNAUTHENTICATED',
                'message' => 'Unauthenticated'
            ], 401);
        }

        $role = $user->role;

        if($role === RoleEnum::DEFAULT ) return response()->json([
            'status' => 'error',
            'code' => 'ROLE_MISSING',
            'message' => 'Forbidden. Akses ditolak pengguna belum memilih role',
        ], 403);

        if(in_array($role->value, $roles)) return $next($request);

        return response()->json([
            "status" => "error",
            "code" => 'INSUFFICIENT_PERMISSION',
            "message" => "Forbidden. Akses ditolak karena peran Anda tidak memiliki izin untuk sumber daya ini."
        ], 403);
    }
}

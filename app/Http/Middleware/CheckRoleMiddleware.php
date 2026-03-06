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
                'status' => 'error',
                'message' => 'Unauthenticated',
                'errors' => [
                    'code' => 'UNAUTHENTICATED',
                ],
            ], 401);
        }

        $role = $user->role;

        if($role === RoleEnum::DEFAULT || $role === null) return response()->json([
            'status' => 'error',
            'message' => 'Forbidden. Akses ditolak pengguna belum memilih role',
            'errors' => [
                'code' => 'ROLE_MISSING',
            ],
        ], 403);

        if(in_array($role->value, $roles)) return $next($request);

        return response()->json([
            'status' => 'error',
            'message' => 'Forbidden. Akses ditolak karena peran Anda tidak memiliki izin untuk sumber daya ini.',
            'errors' => [
                'code' => 'INSUFFICIENT_PERMISSION',
            ],
        ], 403);
    }
}

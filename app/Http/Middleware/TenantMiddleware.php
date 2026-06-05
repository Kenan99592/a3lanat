<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'غير مصرح.'], 401);
        }

        if (!$user->tenant) {
            return response()->json(['message' => 'لا يوجد حساب مرتبط.'], 403);
        }

        if ($user->tenant->status === 'suspended') {
            return response()->json(['message' => 'تم تعليق الحساب.'], 403);
        }

        app()->instance('current_tenant', $user->tenant);

        return $next($request);
    }
}

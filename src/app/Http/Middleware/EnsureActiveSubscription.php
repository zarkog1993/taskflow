<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user || !$user->hasActiveSubscription()) {
            return response()->json([
                'message' => 'An approved and active subscription is required.',
                'code' => 'subscription_required',
            ], 403);
        }

        return $next($request);
    }
}

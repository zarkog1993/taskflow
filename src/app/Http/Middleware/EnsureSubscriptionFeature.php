<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if ($user?->isSuperAdmin() || $user?->hasFeature($feature)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'This feature is not included in your active subscription.',
            'code' => 'feature_not_included',
            'feature' => $feature,
        ], 403);
    }
}

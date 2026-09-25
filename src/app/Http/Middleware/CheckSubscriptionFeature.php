<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscriptionFeature
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        $user = $request->user();

        // Super Admin ima pristup svemu
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $subscription = $user->club?->subscription;

        // 1. Provera da li pretplata postoji i da li je odobrena/aktivna
        if (!$subscription || !in_array($subscription->status, ['approved', 'active'])) {
            return response()->json([
                'message' => 'Vaša pretplata nije aktivna ili čeka odobrenje.'
            ], 403);
        }

        // 2. Provera da li paket sadrži traženu mogućnost
        $features = $subscription->features ?? [];
        if (!in_array($feature, $features)) {
            return response()->json([
                'message' => 'Ova opcija nije uključena u vaš trenutni paket pretplate.'
            ], 403);
        }

        return $next($request);
    }
}

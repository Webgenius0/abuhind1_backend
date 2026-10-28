<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PremiumCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PremiumController extends Controller
{
    public function redeem(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string', 'max:64']]);

        $user = $request->user();
        $code = strtoupper(trim($request->input('code')));

        DB::transaction(function () use ($user, $code) {
            $row = PremiumCode::where('code', $code)->lockForUpdate()->first();

            if (!$row || ($row->assigned_user_id && $row->assigned_user_id !== $user->id)) {
                throw ValidationException::withMessages(['code' => 'Invalid activation code.']);
            }

            if ($row->used_at) {
                throw ValidationException::withMessages(['code' => 'This code has already been used.']);
            }

            if ($row->code_expires_at && $row->code_expires_at->isPast()) {
                throw ValidationException::withMessages(['code' => 'This code has expired.']);
            }

            $alreadyLifetime = $user->is_premium && $user->premium_expires_at === null;

            if ($row->validity_days === null || $alreadyLifetime) {
                $expiresAt = null; // lifetime
            } else {
                $base      = $user->isPremium() ? $user->premium_expires_at : now();
                $expiresAt = $base->copy()->addDays($row->validity_days);
            }

            $user->forceFill([
                'is_premium'         => true,
                'premium_expires_at' => $expiresAt,
            ])->save();

            $row->update(['used_at' => now(), 'used_by' => $user->id]);
        });

        $user = $user->fresh();

        return response()->json([
            'message' => 'Premium activated.',
            'data'    => [
                'is_premium'         => $user->isPremium(),
                'premium_expires_at' => $user->premium_expires_at,
            ],
        ]);
    }
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        $codes = PremiumCode::where('used_by', $user->id)
            ->orderBy('used_at')
            ->get(['code', 'used_at', 'validity_days']);

        $history = [];
        $runningExpiry = null; // Tracks cumulative expiry date matching redeem() stacking logic

        foreach ($codes as $code) {
            if ($code->validity_days === null) {
                $endsAt = null; // lifetime
            } else {
                $base   = ($runningExpiry && $runningExpiry->gt($code->used_at)) ? $runningExpiry : $code->used_at;
                $endsAt = $base->copy()->addDays($code->validity_days);
            }

            $history[] = [
                'plan'       => 'premium',
                'code'       => $code->code,
                'started_at' => $code->used_at,
                'ends_at'    => $endsAt,
            ];

            // Once lifetime is set, subsequent codes cannot shorten it
            if ($runningExpiry !== null || $endsAt === null) {
                $runningExpiry = $endsAt;
            }
        }

        return response()->json([
            'message' => 'Plan history.',
            'data'    => [
                'current_plan'       => $user->isPremium() ? 'premium' : 'free',
                'current_expires_at' => $user->premium_expires_at,
                'history'            => $history,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UserIdMail;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    use ApiResponse;

    /**
     * Handle social authentication (Google, Facebook, Apple, GitHub).
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function socialLogin(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|string|in:google,facebook,apple,github',
            'token'    => 'required|string',
        ]);

        $provider = $request->provider;

        try {
            $socialUser = Socialite::driver($provider)
                ->stateless()
                ->userFromToken($request->token);

            if (!$socialUser || !$socialUser->getEmail()) {
                return $this->error('Invalid ' . ucfirst($provider) . ' account. Email is required.', 401);
            }

            $email = $socialUser->getEmail();
            $user = User::firstOrNew(['email' => $email]);
            $isNewUser = !$user->exists;

            if ($isNewUser) {
                // Register new user with a unique user ID
                $user->user_id = $this->generateUniqueUserId();

                $user->name = $socialUser->getName()
                    ?? $socialUser->getNickname()
                    ?? explode('@', $email)[0]
                    ?? 'User';

                $user->email = $email;
                $user->password = Hash::make(Str::random(32));
                $user->role = 'User';
                $user->email_verified_at = now();
                $user->avatar = $socialUser->getAvatar();
            } else {
                // Refresh avatar for existing user
                $user->avatar = $socialUser->getAvatar() ?? $user->avatar;
            }

            // Sync provider credentials
            $user->provider = $provider;
            $user->provider_id = $socialUser->getId();
            $user->provider_token = $request->token;
            $user->save();

            // Send User ID email ONLY if the user is newly registered
            if ($isNewUser && $user->user_id) {
                try {
                    Mail::to($user->email)->send(new UserIdMail($user, $user->user_id));
                } catch (\Throwable $mailError) {
                    Log::error('Failed to send User ID email after social login', [
                        'email'   => $user->email,
                        'user_id' => $user->user_id,
                        'error'   => $mailError->getMessage(),
                    ]);
                }
            }

            // Revoke previous tokens and issue a fresh session token
            $user->tokens()
                ->where('name', 'SocialAuth-' . $provider)
                ->delete();

            $plainToken = $user->createToken('SocialAuth-' . $provider)->plainTextToken;

            return $this->success('Login successful.', [
                'token_type' => 'Bearer',
                'token'      => $plainToken,
                'user'       => $user,
            ]);

        } catch (\Throwable $e) {
            Log::error('Social Login Failed', [
                'provider' => $provider,
                'message'  => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return $this->error('Invalid credentials.', null, 401);
        }
    }

    /**
     * Authenticate directly using a unique User ID.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function loginWithUserId(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|string|exists:users,user_id',
        ]);

        $user = User::where('user_id', $request->user_id)->first();

        if (!$user) {
            return $this->error('Invalid User ID.', null, 404);
        }

        // Revoke prior tokens and issue new auth token
        $user->tokens()->where('name', 'UserIdAuth')->delete();
        $plainToken = $user->createToken('UserIdAuth')->plainTextToken;
        $user->setAttribute('onboarding_completed', $user->onboarding_completed);

        return $this->success('Login successful via User ID.', [
            'token_type' => 'Bearer',
            'token'      => $plainToken,
            'user'       => $user,
        ], 200);
    }

    /**
     * Generate a unique alphanumeric User ID (e.g., USR-A1B2C3).
     *
     * @return string
     */
    private function generateUniqueUserId(): string
    {
        do {
            $userId = 'USR-' . strtoupper(Str::random(6));
        } while (User::where('user_id', $userId)->exists());

        return $userId;
    }

    /**
     * Complete user onboarding profile.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function completeOnboarding(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_18_plus' => 'required|boolean',
            'gender'     => 'required|string|in:male,female,other',
            'background' => 'required|string|max:100', // e.g., asian, african, blackasian
        ]);

        $user = $request->user();
        $user->update([
            'is_18_plus' => $validated['is_18_plus'],
            'gender'     => $validated['gender'],
            'background' => $validated['background'],
        ]);

        return $this->success('Onboarding completed successfully.', [
            'user' => $user,
        ]);
    }
}

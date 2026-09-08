<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/social-login
     * Social Login / Registration
     */
    public function socialLogin(Request $request)
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

            if (!$user->exists) {
                // ১. নতুন ইউজার হলে ইউনিক User ID তৈরি করা
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
                // পুরানো ইউজার হলে অবতার রিফ্রেশ করা
                $user->avatar = $socialUser->getAvatar() ?? $user->avatar;
            }

            // Provider তথ্য আপডেট
            $user->provider = $provider;
            $user->provider_id = $socialUser->getId();
            $user->provider_token = $request->token;
            $user->save();

            // আগের সেশন টোকেন ডিলিট
            $user->tokens()
                ->where('name', 'SocialAuth-' . $provider)
                ->delete();

            // নতুন টোকেন ইস্যু
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
            ]);

            return $this->error(ucfirst($provider) . ' authentication failed.', 401);
        }
    }

    /**
     * POST /api/login-with-user-id
     * Unique User ID দিয়ে সরাসরি লগইন
     */
    public function loginWithUserId(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|exists:users,user_id',
        ]);

        $user = User::where('user_id', $request->user_id)->first();

        if (!$user) {
            return $this->error('Invalid User ID.', 404);
        }

        // টোকেন তৈরি
        $plainToken = $user->createToken('UserIdAuth')->plainTextToken;

        return $this->success('Login successful via User ID.', [
            'token_type' => 'Bearer',
            'token'      => $plainToken,
            'user'       => $user,
        ]);
    }

    /**
     * ইউনিক User ID জেনারেট করার ফাংশন (যেমন: USR-A1B2C3)
     */
    private function generateUniqueUserId(): string
    {
        do {
            // USR- এর পর ৬ ডিজিটের র‍্যান্ডম আলফানিউমেরিক কোড তৈরি
            $userId = 'USR-' . strtoupper(Str::random(6));
        } while (User::where('user_id', $userId)->exists());

        return $userId;
    }
}

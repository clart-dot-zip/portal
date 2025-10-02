<?php

namespace App\Http\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('authentik')->redirect();
    }

    public function callback()
    {
        try {
            $authentikUser = Socialite::driver('authentik')->user();

            // First, try to find user by authentik_id (most reliable)
            $user = User::where('authentik_id', $authentikUser->getId())->first();
            
            if (!$user) {
                // If not found by authentik_id, try by email
                $user = User::where('email', $authentikUser->getEmail())->first();
                
                if ($user) {
                    // User exists with same email but different authentik_id
                    // This could be a legitimate ID change in Authentik
                    // Log this for monitoring
                    Log::warning('User authentik_id changed during login', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'old_authentik_id' => $user->authentik_id,
                        'new_authentik_id' => $authentikUser->getId()
                    ]);
                    
                    // Update the authentik_id only if it's clearly different
                    if ($user->authentik_id != $authentikUser->getId()) {
                        $user->update([
                            'authentik_id' => $authentikUser->getId(),
                            'name' => $authentikUser->getName(),
                            'avatar' => $authentikUser->getAvatar(),
                        ]);
                    }
                } else {
                    // Create new user
                    $user = User::create([
                        'email' => $authentikUser->getEmail(),
                        'name' => $authentikUser->getName(),
                        'authentik_id' => $authentikUser->getId(),
                        'avatar' => $authentikUser->getAvatar(),
                    ]);
                }
            } else {
                // User found by authentik_id, just update name and avatar
                $user->update([
                    'name' => $authentikUser->getName(),
                    'avatar' => $authentikUser->getAvatar(),
                ]);
            }

            Auth::login($user, true);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Log::error('Authentication callback failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->withErrors(['error' => 'Authentication failed.']);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Authentik\AuthentikSDK;

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
            
            // Get the correct numeric ID from Authentik API using the email
            $authentikSDK = new AuthentikSDK();
            $apiUser = $authentikSDK->users()->getByEmail($authentikUser->getEmail());
            
            $correctAuthentikId = null;
            if (!empty($apiUser) && isset($apiUser['pk'])) {
                $correctAuthentikId = (string) $apiUser['pk'];
            }
            
            // Check if user exists by correct authentik_id first
            $user = null;
            if ($correctAuthentikId) {
                $user = User::where('authentik_id', $correctAuthentikId)->first();
            }
            
            if (!$user) {
                // If not found by authentik_id, check by email
                $user = User::where('email', $authentikUser->getEmail())->first();
                
                if ($user) {
                    // Log warning if authentik_id is changing
                    Log::warning('User authentik_id being corrected during login', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'old_authentik_id' => $user->authentik_id,
                        'socialite_id' => $authentikUser->getId(),
                        'correct_authentik_id' => $correctAuthentikId
                    ]);
                    
                    // Update to use the correct numeric ID
                    if ($correctAuthentikId && $user->authentik_id != $correctAuthentikId) {
                        $user->update([
                            'authentik_id' => $correctAuthentikId,
                            'name' => $authentikUser->getName(),
                            'avatar' => $authentikUser->getAvatar(),
                        ]);
                    }
                } else {
                    // Create new user with correct numeric ID
                    $user = User::create([
                        'email' => $authentikUser->getEmail(),
                        'name' => $authentikUser->getName(),
                        'authentik_id' => $correctAuthentikId ?: $authentikUser->getId(),
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
        
        // Clear the session completely
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        // Redirect to welcome page with a message
        return redirect()->route('welcome')->with('status', 'You have been logged out of Portal successfully.');
    }
}

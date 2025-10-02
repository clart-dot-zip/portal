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
            
            Log::info('Authentication callback data', [
                'socialite_id' => $authentikUser->getId(),
                'socialite_email' => $authentikUser->getEmail(),
                'socialite_name' => $authentikUser->getName()
            ]);
            
            // First try to find user by the exact email from Socialite
            $user = User::where('email', $authentikUser->getEmail())->first();
            
            // If user exists, check if their authentik_id needs correction
            if ($user) {
                // Only do API lookup if the authentik_id looks wrong (is a UUID instead of numeric)
                if (strlen($user->authentik_id) > 10) { // UUID is much longer than numeric ID
                    $authentikSDK = new AuthentikSDK();
                    $apiUser = $authentikSDK->users()->getByEmail($authentikUser->getEmail());
                    
                    if (!empty($apiUser) && isset($apiUser['pk']) && $apiUser['email'] === $authentikUser->getEmail()) {
                        $correctAuthentikId = (string) $apiUser['pk'];
                        
                        Log::warning('Correcting authentik_id for existing user', [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'old_authentik_id' => $user->authentik_id,
                            'new_authentik_id' => $correctAuthentikId
                        ]);
                        
                        $user->update([
                            'authentik_id' => $correctAuthentikId,
                            'name' => $authentikUser->getName(),
                            'avatar' => $authentikUser->getAvatar(),
                        ]);
                    } else {
                        // Just update name and avatar, keep existing authentik_id
                        $user->update([
                            'name' => $authentikUser->getName(),
                            'avatar' => $authentikUser->getAvatar(),
                        ]);
                    }
                } else {
                    // authentik_id looks correct, just update name and avatar
                    $user->update([
                        'name' => $authentikUser->getName(),
                        'avatar' => $authentikUser->getAvatar(),
                    ]);
                }
            } else {
                // No user found with this email, create new user
                Log::info('Creating new user from Authentik login', [
                    'email' => $authentikUser->getEmail(),
                    'name' => $authentikUser->getName(),
                    'socialite_id' => $authentikUser->getId()
                ]);
                
                $user = User::create([
                    'email' => $authentikUser->getEmail(),
                    'name' => $authentikUser->getName(),
                    'authentik_id' => $authentikUser->getId(), // Use Socialite ID directly for new users
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

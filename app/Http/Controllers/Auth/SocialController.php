<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\User\RoleName;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

final class SocialController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                // Update user with google info if not set
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar'    => $googleUser->avatar,
                ]);
            } else {
                // Create a new user
                $mahasiswaRole = Role::where('role_name', RoleName::MAHASISWA->value)->first();
                
                $user = User::create([
                    'name'        => $googleUser->name,
                    'email'       => $googleUser->email,
                    'google_id'   => $googleUser->id,
                    'avatar'      => $googleUser->avatar,
                    'role_id'     => $mahasiswaRole?->id,
                    'password'    => null, // Password is null for social login
                    'is_approved' => true, // Auto approve social login? Or set based on your needs
                ]);
            }

            Auth::login($user);

            return redirect()->intended($user->isMahasiswa() ? '/mahasiswa/dashboard' : '/admin/dashboard');
            
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }
}

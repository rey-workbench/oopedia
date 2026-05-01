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
use Inertia\Response;
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

                Auth::login($user);

                // Check if user is approved
                if (! $user->is_approved) {
                    return to_route('admin.pending-approval');
                }

                return redirect()->intended($user->isMahasiswa() ? '/mahasiswa/dashboard' : '/admin/dashboard');
            }

            // New user: Store data in session temporarily
            session(['google_user' => [
                'google_id' => $googleUser->id,
                'name'      => $googleUser->name,
                'email'     => $googleUser->email,
                'avatar'    => $googleUser->avatar,
            ]]);

            return to_route('auth.google.choose-role');
        } catch (Exception $exception) {
            return to_route('login')->with('error', 'Gagal login dengan Google: ' . $exception->getMessage());
        }
    }

    /**
     * Show the role selection page for new Google users.
     */
    public function chooseRole(): Response|RedirectResponse
    {
        if (! session()->has('google_user')) {
            return to_route('login');
        }

        return $this->render('Auth/Social/ChooseRole', [
            'googleUser' => session('google_user'),
        ]);
    }

    /**
     * Register the new Google user with the chosen role.
     */
    public function register(string $role): RedirectResponse
    {
        if (! session()->has('google_user')) {
            return to_route('login');
        }

        $googleData = session('google_user');

        $roleEnum = $role === 'dosen' ? RoleName::DOSEN : RoleName::MAHASISWA;
        $dbRole   = Role::where('role_name', $roleEnum->value)->first();

        if (! $dbRole) {
            return to_route('login')->with('error', 'Peran tidak ditemukan.');
        }

        $isApproved = ($roleEnum === RoleName::MAHASISWA);

        $user = User::create([
            'name'        => $googleData['name'],
            'email'       => $googleData['email'],
            'google_id'   => $googleData['google_id'],
            'avatar'      => $googleData['avatar'],
            'role_id'     => $dbRole->id,
            'password'    => null,
            'is_approved' => $isApproved,
        ]);

        session()->forget('google_user');

        Auth::login($user);

        if (! $isApproved) {
            return to_route('admin.pending-approval');
        }

        return to_route('mahasiswa.dashboard');
    }
}

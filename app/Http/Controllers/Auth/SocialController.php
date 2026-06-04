<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;

final class SocialController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService,
    ) {}

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $googleData = [
                'google_id' => $googleUser->id,
                'name'      => $googleUser->name,
                'email'     => $googleUser->email,
                'avatar'    => $googleUser->avatar,
            ];

            $user = $this->userService->findOrCreateSocialUser($googleData);

            if ($user instanceof User) {
                Auth::login($user);

                if (! $user->is_approved) {
                    return to_route('admin.pending-approval');
                }

                return redirect()->intended($user->isMahasiswa() ? '/mahasiswa/dashboard' : '/admin/dashboard');
            }

            session(['google_user' => $googleData]);

            return to_route('auth.google.choose-role');
        } catch (Exception $exception) {
            report($exception);

            return to_route('login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }

    public function chooseRole(): Response|RedirectResponse
    {
        if (! session()->has('google_user')) {
            return to_route('login');
        }

        return $this->render('Auth/Social/ChooseRole', [
            'googleUser' => session('google_user'),
        ]);
    }

    public function register(string $role): RedirectResponse
    {
        if (! session()->has('google_user')) {
            return to_route('login');
        }

        $googleData = session('google_user');
        $user       = $this->userService->registerSocialUser($googleData, $role);

        session()->forget('google_user');

        Auth::login($user);

        if (! $user->is_approved) {
            return to_route('admin.pending-approval');
        }

        return to_route('mahasiswa.dashboard');
    }
}

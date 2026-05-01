<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Inertia\Response;

final class ForgotPasswordController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return $this->render('Auth/ForgotPassword');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(ForgotPasswordRequest $request): RedirectResponse
    {

        // We will send the password reset link to this user. Once it has been sent
        // we will examine the response then see the message we need to show to the user.
        // Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email'),
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}

<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\ValidationException;

class AuthenticateUser
{
    public function __invoke($request, $next)
    {
        $email = $request->input(Fortify::username());

        $user = User::where(
            Fortify::username(),
            $email
        )->first();

        /*
        |--------------------------------------------------------------------------
        | User not found
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            throw ValidationException::withMessages([
                Fortify::username() => __('auth.failed'),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Locked Account
        |--------------------------------------------------------------------------
        */

        if (
            $user->locked_until &&
            $user->locked_until->isFuture()
        ) {
            $minutes = now()->diffInMinutes(
                $user->locked_until
            ) + 1;

            throw ValidationException::withMessages([
                Fortify::username() =>
                "Your account is temporarily locked. Please try again in {$minutes} minute(s).",
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Password
        |--------------------------------------------------------------------------
        */

        if (!Hash::check(
            $request->input('password'),
            $user->password
        )) {

            $user->increment('login_attempts');

            $maxAttempts = 5;

            /*
            |--------------------------------------------------------------------------
            | Lock Account
            |--------------------------------------------------------------------------
            */

            if ($user->login_attempts >= $maxAttempts) {

                $user->update([
                    'login_attempts' => 0,
                    'locked_until' => now()->addMinutes(1),
                ]);

                throw ValidationException::withMessages([
                    Fortify::username() =>
                    'Too many failed login attempts. Your account has been locked for 15 minutes.',
                ]);
            }

            $remaining =
                $maxAttempts -
                $user->login_attempts;

            throw ValidationException::withMessages([
                Fortify::username() =>
                "Invalid credentials. {$remaining} attempt(s) remaining.",
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Successful Authentication
        |--------------------------------------------------------------------------
        */

        $user->update([
            'login_attempts' => 0,
            'locked_until' => null,
        ]);

        return $next($request);
    }
}

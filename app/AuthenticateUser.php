<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticateUser
{
    public function authenticate(array $credentials): User
    {
        $user = User::where('email', $credentials['email'])->first();

        /*
        |--------------------------------------------------------------------------
        | User does not exist
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Account Lock
        |--------------------------------------------------------------------------
        */

        if (
            $user->locked_until &&
            $user->locked_until->isFuture()
        ) {
            throw ValidationException::withMessages([
                'email' => 'Your account is temporarily locked. Please try again later.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Password Check
        |--------------------------------------------------------------------------
        */

        if (!Hash::check($credentials['password'], $user->password)) {

            $user->increment('login_attempts');

            /*
            |--------------------------------------------------------------------------
            | Maximum Attempts
            |--------------------------------------------------------------------------
            */

            $maxAttempts = 5;

            if ($user->login_attempts >= $maxAttempts) {

                $user->update([
                    'locked_until' => now()->addMinutes(15),
                    'login_attempts' => 0,
                ]);

                throw ValidationException::withMessages([
                    'email' => 'Too many failed login attempts. Your account has been temporarily locked for 15 minutes.',
                ]);
            }

            $remaining = $maxAttempts - $user->login_attempts;

            throw ValidationException::withMessages([
                'email' => "Invalid credentials. You have {$remaining} login attempt(s) remaining.",
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Successful Login
        |--------------------------------------------------------------------------
        */

        $user->update([
            'login_attempts' => 0,
            'locked_until' => null,
        ]);

        return $user;
    }
}

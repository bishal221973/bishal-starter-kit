<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Facades\AppConfig;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Fortify Actions
        |--------------------------------------------------------------------------
        */

        Fortify::createUsersUsing(
            CreateNewUser::class
        );

        Fortify::updateUserProfileInformationUsing(
            UpdateUserProfileInformation::class
        );

        Fortify::updateUserPasswordsUsing(
            UpdateUserPassword::class
        );

        Fortify::resetUserPasswordsUsing(
            ResetUserPassword::class
        );

        Fortify::redirectUserForTwoFactorAuthenticationUsing(
            RedirectIfTwoFactorAuthenticatable::class
        );


        /*
        |--------------------------------------------------------------------------
        | Login Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('login', function (Request $request) {

            $throttleKey = Str::transliterate(
                Str::lower(
                    $request->input(
                        Fortify::username()
                    )
                ) . '|' . $request->ip()
            );

            return Limit::perMinute(5)
                ->by($throttleKey);
        });


        /*
        |--------------------------------------------------------------------------
        | Two Factor Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('two-factor', function (
            Request $request
        ) {

            return Limit::perMinute(5)
                ->by(
                    $request
                        ->session()
                        ->get('login.id')
                );
        });


        /*
        |--------------------------------------------------------------------------
        | Passkeys Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('passkeys', function (
            Request $request
        ) {

            $credentialId = $request->input(
                'credential.id'
            );

            return Limit::perMinute(10)
                ->by(
                    (
                        $credentialId
                        ?: $request->session()->get('login.id')
                    ) . '|' . $request->ip()
                );
        });


        /*
        |--------------------------------------------------------------------------
        | Custom Authentication
        |--------------------------------------------------------------------------
        */

        Fortify::authenticateUsing(
            function (Request $request) {

                /*
                |--------------------------------------------------------------------------
                | Find User
                |--------------------------------------------------------------------------
                */

                $username = Fortify::username();

                $user = User::where(
                    $username,
                    $request->input($username)
                )->first();

                /*
                |--------------------------------------------------------------------------
                | User Does Not Exist
                |--------------------------------------------------------------------------
                */

                if (!$user) {

                    throw ValidationException::withMessages([
                        $username => 'Invalid credentials.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Login Security Configuration
                |--------------------------------------------------------------------------
                */

                $loginSecurity = AppConfig::loginSecurity();

                $attemptLimitEnabled =
                    (bool) (
                        $loginSecurity['enabled']
                        ?? false
                    );

                $maxAttempts = max(
                    (int) (
                        $loginSecurity['max_attempts']
                        ?? 5
                    ),
                    1
                );

                $lockoutDuration = max(
                    (int) (
                        $loginSecurity['lockout_duration']
                        ?? 15
                    ),
                    1
                );

                /*
                |--------------------------------------------------------------------------
                | Check Existing Account Lock
                |--------------------------------------------------------------------------
                */

                if (
                    $attemptLimitEnabled &&
                    $user->locked_until
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Still Active
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $user->locked_until->isFuture()
                    ) {

                        $minutes = max(
                            now()->diffInMinutes(
                                $user->locked_until
                            ),
                            0
                        ) + 1;

                        throw ValidationException::withMessages([
                            $username =>
                            "Your account is temporarily locked. Please try again in {$minutes} minute(s).",
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Expired
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    | Reset the attempt counter when the lock period
                    | has completely expired.
                    |
                    */

                    $user->update([
                        'login_attempts' => 0,
                        'locked_until' => null,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Check Password
                |--------------------------------------------------------------------------
                */

                if (
                    !Hash::check(
                        $request->password,
                        $user->password
                    )
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Login Attempt Protection Disabled
                    |--------------------------------------------------------------------------
                    |
                    | Unlimited failed login attempts.
                    |
                    */

                    if (!$attemptLimitEnabled) {

                        throw ValidationException::withMessages([
                            $username =>
                            'Invalid credentials.',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Increment Failed Attempts
                    |--------------------------------------------------------------------------
                    */

                    $user->increment(
                        'login_attempts'
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Refresh User
                    |--------------------------------------------------------------------------
                    |
                    | Make sure we use the latest value after increment().
                    |
                    */

                    $user->refresh();

                    /*
                    |--------------------------------------------------------------------------
                    | Maximum Attempts Reached
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $user->login_attempts >=
                        $maxAttempts
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | Lock Account
                        |--------------------------------------------------------------------------
                        */

                        $user->update([
                            'locked_until' =>
                            now()->addMinutes(
                                $lockoutDuration
                            ),
                        ]);

                        throw ValidationException::withMessages([
                            $username =>
                            "Too many failed login attempts. Your account has been locked for {$lockoutDuration} minutes.",
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Remaining Attempts
                    |--------------------------------------------------------------------------
                    */

                    $remaining =
                        $maxAttempts -
                        $user->login_attempts;

                    throw ValidationException::withMessages([
                        $username =>
                        "Invalid credentials. {$remaining} attempt(s) remaining.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Successful Login
                |--------------------------------------------------------------------------
                |
                | Always clear login attempts and lock information.
                |
                */

                $user->update([
                    'login_attempts' => 0,
                    'locked_until' => null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Authentication Successful
                |--------------------------------------------------------------------------
                */

                return $user;
            }
        );
    }
}

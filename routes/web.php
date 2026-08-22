<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\MailSettingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\SettingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::resource('organizations', OrganizationController::class)->middleware(['auth:sanctum', 'password.expired', 'conditional.verified']);
Route::middleware(['has.organization'])->group(function () {



    Route::middleware([
        'auth:sanctum',
        'password.expired',
        'conditional.verified',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
        Route::prefix('settings')->group(function () {
            Route::get('theme-setting', [SettingController::class, 'theme'])->name('theme.setting');
            Route::post('theme-setting-update', [SettingController::class, 'themeUpdate'])->name('theme.setting.update');
        });

        Route::prefix('configurations')->group(function () {
            Route::get('configuration', [ConfigurationController::class, 'index'])->name('configuration.setting');
            Route::post('configuration-update', [MailSettingController::class, 'Update'])->name('configuration.mail.setting.update');
        });

        Route::resource('branches',BranchController::class);
    });




    Route::middleware(['auth'])->group(function () {
        Route::post('configuration-update', [ConfigurationController::class, 'Update'])->name('configuration.setting.update');

        Route::get('/change-password', [
            PasswordChangeController::class,
            'create'
        ])->name('password.change');

        Route::post('/change-password', [
            PasswordChangeController::class,
            'store'
        ])->name('password.change.store');
        Route::get('verify-email', [EmailVerifyController::class, 'index'])->name('verification.notice');
        Route::post('verify-email/send', [EmailVerifyController::class, 'send'])->name('verification.send');
        Route::get('/email/verify/{id}/{hash}', [
            EmailVerifyController::class,
            'verify',
        ])
            ->middleware('signed')
            ->name('verification.verify');
    });
});

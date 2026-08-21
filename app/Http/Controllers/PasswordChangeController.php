<?php

namespace App\Http\Controllers;

use App\Facades\AppConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class PasswordChangeController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/ChangePassword');
    }

    public function store(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => AppConfig::password(),
        ]);

        $expiryDays = AppConfig::passwordExpiryDays();
        $user=User::find(auth()->id());
        $user->update([
            'password' => Hash::make($request->password),
            'password_created_at' => now(),
            'password_expired_at' => $expiryDays
                ? now()->addDays($expiryDays)
                : null,
        ]);

        return redirect()->route('dashboard');
    }
}

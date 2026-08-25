<?php

namespace App\Http\Controllers;

use App\Facades\AppConfig;
use App\Models\OrganizationUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user()->load(['info', 'documents']);
        return Inertia::render('Admin/Setting/Profile', [
            'user' => $user,
        ]);
    }

    public function personalInfo(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|max:255',
            'gender'         => 'nullable',
            'date_of_birth'  => 'nullable|date',
            'personal_phone' => 'required|max:20',
            'address'        => 'nullable',
            'city'           => 'nullable',
            'state'          => 'nullable',
            'country'        => 'nullable',
            'postal_code'    => 'nullable|max:20',
            'tax_number'     => 'nullable|max:100',
        ]);

        $user = Auth::user();

        // Update users table
        $user->update([
            'name' => $data['name'],
        ]);

        // return OrganizationUser::where('user_id', $user->id)->first();
        // Update organization_users table
        OrganizationUser::where('user_id', $user->id)->first()->update([
            'gender'         => $data['gender'] ?? null,
            'date_of_birth'  => $data['date_of_birth'] ?? null,
            'personal_phone' => $data['personal_phone'],
            'address'        => $data['address'] ?? null,
            'city'           => $data['city'] ?? null,
            'state'          => $data['state'] ?? null,
            'country'        => $data['country'] ?? null,
            'postal_code'    => $data['postal_code'] ?? null,
            'tax_number'     => $data['tax_number'] ?? null,
        ]);

        return back()->with('success', 'Personal information updated successfully.');
    }

    public function updatePassword(Request $request)
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

        return back()->with('success', 'Password updated successfully.');
    }
}

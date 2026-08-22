<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        if ($user->organizations()->exists()) {
            return redirect()->route('dashboard');
        }
        return Inertia::render('Admin/Setting/CreateOrganization');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'nullable',
            'phone' => 'nullable',
            'website' => 'nullable',
            'logo' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'country' => 'nullable',
            'postal_code' => 'nullable',
            'is_active' => 'nullable',
            'subscription_status' => 'nullable'
        ]);
        $slug = Str::slug($request->name);

        $userId = Auth::id();
        $data['slug'] = $slug;
        $org = Organization::create($data);
        OrganizationUser::create([
            'organization_id' => $org->id,
            'user_id' => $userId,
            'employee_code'   => now()->format('Y_m_d') . '_' . $org->id . '_' . $userId,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Organization has been created and you have been added successfully.');
    }
}

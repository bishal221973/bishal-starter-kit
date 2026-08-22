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
            'subscription_status' => 'nullable',
            'parent_id' => 'nullable'
        ]);

        // return $request;
        $slug = Str::slug($request->name);

        $originalSlug = $slug;
        $count = 1;

        while (Organization::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $userId = Auth::id();
        $data['slug'] = $slug;
        $org = Organization::create($data);
        if (!$request?->parent_id) {
            OrganizationUser::create([
                'organization_id' => $org->id,
                'user_id' => $userId,
                'employee_code'   => now()->format('Y_m_d') . '_' . $org->id . '_' . $userId,
            ]);
            return redirect()
                ->route('dashboard')
                ->with('success', 'Organization has been created and you have been added successfully.');
        }

        return redirect()
            ->route('branches.index')
            ->with('success', 'New branch have been created successfully.');
    }

    public function update(
        Request $request,
        Organization $organization
    ) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'logo' => 'nullable',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'subscription_status' => 'nullable|string',
            'parent_id' => 'nullable|exists:organizations,id',
        ]);

        // Only regenerate slug when the name changes
        if ($organization->name !== $data['name']) {
            $slug = Str::slug($data['name']);

            $originalSlug = $slug;
            $count = 1;

            while (
                Organization::where('slug', $slug)
                ->where('id', '!=', $organization->id)
                ->exists()
            ) {
                $slug = $originalSlug . '-' . $count++;
            }

            $data['slug'] = $slug;
        }

        $organization->update($data);

        if (!$organization->parent_id) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'success',
                    'Organization has been updated successfully.'
                );
        }

        return redirect()
            ->route('branches.index')
            ->with(
                'success',
                'Branch has been updated successfully.'
            );
    }
}

<?php

namespace App\Http\Controllers;

use App\Facades\OrganizationConfig;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index1(Request $request)
    {
        $branches = Organization::query()->latest()->where('parent_id', OrganizationConfig::current()?->id)
            ->dataTablePaginate(
                $request
            );

        return Inertia::render('Admin/Manage/Branch/Index', [
            'branches' => $branches,
        ]);
    }

    public function index(Request $request)
    {
        $query = Organization::query();

        /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search')) {
            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

        $filters = $request->input('filters', []);

        if (!empty($filters['status'])) {
            $query->where(
                'status',
                $filters['status']
            );
        }

        if (
            isset($filters['active']) &&
            $filters['active'] !== ''
        ) {
            $query->where(
                'active',
                $filters['active']
            );
        }

        if (!empty($filters['name'])) {
            $query->where(
                'name',
                'like',
                '%' . $filters['name'] . '%'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | ID range
    |--------------------------------------------------------------------------
    */

        if (!empty($filters['id']['min'])) {
            $query->where(
                'id',
                '>=',
                $filters['id']['min']
            );
        }

        if (!empty($filters['id']['max'])) {
            $query->where(
                'id',
                '<=',
                $filters['id']['max']
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Created date range
    |--------------------------------------------------------------------------
    */

        if (!empty($filters['created_at']['from'])) {
            $query->whereDate(
                'created_at',
                '>=',
                $filters['created_at']['from']
            );
        }

        if (!empty($filters['created_at']['to'])) {
            $query->whereDate(
                'created_at',
                '<=',
                $filters['created_at']['to']
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

        $allowedSorts = [
            'id',
            'name',
            'email',
            'status',
            'active',
            'created_at',
        ];

        $sort = $request->input(
            'sort',
            'id'
        );

        $direction = $request->input(
            'direction',
            'asc'
        );

        if (!in_array(
            $sort,
            $allowedSorts,
            true
        )) {
            $sort = 'id';
        }

        if (!in_array(
            $direction,
            ['asc', 'desc'],
            true
        )) {
            $direction = 'asc';
        }

        $query->orderBy(
            $sort,
            $direction
        );

        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

        $perPage = min(
            max(
                (int) $request->input(
                    'per_page',
                    10
                ),
                1
            ),
            100
        );

        $branches = $query
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render(
            'Admin/Manage/Branch/Index',
            [
                'branches' => $branches,
            ]
        );
    }


    public function create()
    {
        return Inertia::render('Admin/Setting/CreateOrganization', [
            'parent' => OrganizationConfig::current()
        ]);
    }

    public function edit(Organization $organization)
    {
        return Inertia::render('Admin/Setting/CreateOrganization', [
            'parent' => OrganizationConfig::current(),
            'organization' => $organization
        ]);
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();
        redirect()->route('branches.index')->with('success', 'Selected organization have been removed.');
    }
}

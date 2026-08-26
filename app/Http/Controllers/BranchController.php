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
            $search = $request->input('search');

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

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where(
                'status',
                $filters['status']
            );
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where(
                'active',
                $filters['active']
            );
        }

        if (
            isset($filters['name']) &&
            $filters['name'] !== ''
        ) {
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

        if (isset($filters['id']['min'])) {
            $query->where(
                'id',
                '>=',
                $filters['id']['min']
            );
        }

        if (isset($filters['id']['max'])) {
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

        if (
            isset($filters['created_at']['from']) &&
            $filters['created_at']['from'] !== ''
        ) {
            $query->whereDate(
                'created_at',
                '>=',
                $filters['created_at']['from']
            );
        }

        if (
            isset($filters['created_at']['to']) &&
            $filters['created_at']['to'] !== ''
        ) {
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

        if (
            !in_array(
                $sort,
                $allowedSorts,
                true
            )
        ) {
            $sort = 'id';
        }

        if (
            !in_array(
                $direction,
                ['asc', 'desc'],
                true
            )
        ) {
            $direction = 'asc';
        }

        $query->orderBy(
            $sort,
            $direction
        );

        /*
        |--------------------------------------------------------------------------
        | SERVER EXPORT
        |--------------------------------------------------------------------------
        |
        | Same /branches route.
        |
        */

        if ($request->boolean('datatable_export')) {

            /*
            |--------------------------------------------------------------------------
            | Selected
            |--------------------------------------------------------------------------
            */

            if (
                $request->input('export_scope') ===
                'selected'
            ) {
                $selectedIds =
                    $request->input(
                        'selected_ids',
                        []
                    );

                if (
                    !is_array(
                        $selectedIds
                    )
                ) {
                    $selectedIds = [];
                }

                if (
                    empty($selectedIds)
                ) {
                    return response()->json([
                        'data' => [],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | IMPORTANT
                |--------------------------------------------------------------------------
                |
                | Apply the user's selected IDs.
                |
                */

                $query->whereIn(
                    'id',
                    $selectedIds
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Return ALL matching records
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'data' =>
                    $query
                        ->get()
                        ->values(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Normal pagination
        |--------------------------------------------------------------------------
        */

        $branches = $query
            ->paginate(
                $request->integer(
                    'per_page',
                    10
                )
            )
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Inertia page
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Admin/Manage/Branch/Index',
            [
                'branches' => $branches,
            ]
        );
    }
    public function index2(Request $request)
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

    public function show(Request $request)
{
    $query = Organization::query();

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
        $search = $request->string('search')->toString();

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

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    if (
        isset($filters['status']) &&
        $filters['status'] !== ''
    ) {
        $query->where(
            'status',
            $filters['status']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Active
    |--------------------------------------------------------------------------
    */

    if (
        isset($filters['active']) &&
        $filters['active'] !== ''
    ) {
        $query->where(
            'active',
            $filters['active']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Name
    |--------------------------------------------------------------------------
    */

    if (
        !empty($filters['name'])
    ) {
        $query->where(
            'name',
            'like',
            '%' .
                $filters['name'] .
                '%'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ID range
    |--------------------------------------------------------------------------
    */

    if (
        isset(
            $filters['id']['min']
        ) &&
        $filters['id']['min'] !== ''
    ) {
        $query->where(
            'id',
            '>=',
            $filters['id']['min']
        );
    }

    if (
        isset(
            $filters['id']['max']
        ) &&
        $filters['id']['max'] !== ''
    ) {
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

    if (
        !empty(
            $filters['created_at']['from']
        )
    ) {
        $query->whereDate(
            'created_at',
            '>=',
            $filters['created_at']['from']
        );
    }

    if (
        !empty(
            $filters['created_at']['to']
        )
    ) {
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

    if (
        !in_array(
            $sort,
            $allowedSorts,
            true
        )
    ) {
        $sort = 'id';
    }

    if (
        !in_array(
            $direction,
            ['asc', 'desc'],
            true
        )
    ) {
        $direction = 'asc';
    }

    $query->orderBy(
        $sort,
        $direction
    );

    /*
    |--------------------------------------------------------------------------
    | Selected records
    |--------------------------------------------------------------------------
    */

    if (
        $request->boolean(
            'select_all_filtered'
        )
    ) {
        /*
        |--------------------------------------------------------------------------
        | All filtered records are selected.
        |
        | Remove manually excluded records.
        |--------------------------------------------------------------------------
        */

        $excludedIds =
            $request->input(
                'excluded_ids',
                []
            );

        if (
            !empty($excludedIds)
        ) {
            $query->whereNotIn(
                'id',
                $excludedIds
            );
        }
    } elseif (
        $request->has(
            'selected_ids'
        )
    ) {
        /*
        |--------------------------------------------------------------------------
        | Only explicitly selected IDs
        |--------------------------------------------------------------------------
        */

        $selectedIds =
            $request->input(
                'selected_ids',
                []
            );

        if (
            !empty($selectedIds)
        ) {
            $query->whereIn(
                'id',
                $selectedIds
            );
        } else {
            return response()->json([
                'data' => [],
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORTANT
    |--------------------------------------------------------------------------
    |
    | DO NOT paginate here.
    |
    | get() returns every matching record.
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'data' => $query->get(),
    ]);
}

public function show1(){

}
}

<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Traits\HandlesDataTable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    use HandlesDataTable;

    // public function index(Request $request)
    // {
    //     $branches = Organization::where('parent_id', 1)->latest()->get();
    //     $branches1 = Organization::query()
    //         ->dataTablePaginate($request);
    //     return Inertia::render('Admin/Manage/Branch/Index', [
    //         'branches' => $branches,
    //         'branches1' => $branches1
    //     ]);
    // }

    public function index(
        Request $request
    ) {
        $organizations =
            $this->dataTable(
                Organization::query(),

                $request,

                /*
                |--------------------------------------------------------------------------
                | Searchable
                |--------------------------------------------------------------------------
                */

                [
                    'name',
                    'slug',
                    'email',
                    'phone',
                    'vat',
                ],

                /*
                |--------------------------------------------------------------------------
                | Filterable
                |--------------------------------------------------------------------------
                */

                [
                    'status',
                    'parent_id',
                ],

                /*
                |--------------------------------------------------------------------------
                | Sortable
                |--------------------------------------------------------------------------
                */

                [
                    'name',
                    'slug',
                    'email',
                    'phone',
                    'vat',
                    'created_at',
                ],

                /*
                |--------------------------------------------------------------------------
                | Default per page
                |--------------------------------------------------------------------------
                */

                10,

                /*
                |--------------------------------------------------------------------------
                | Per page options
                |--------------------------------------------------------------------------
                */

                [
                    10,
                    25,
                    50,
                    100,
                ]
            );

        return Inertia::render(
            'Admin/Manage/Branch/Index',
            [
                'organizations' =>
                $organizations,
            ]
        );
    }
}

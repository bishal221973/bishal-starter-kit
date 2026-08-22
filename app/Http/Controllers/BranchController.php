<?php

namespace App\Http\Controllers;

use App\Facades\OrganizationConfig;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $branches = Organization::query()->latest()->where('parent_id',OrganizationConfig::current()?->id)
            ->dataTablePaginate(
                $request
            );

        return Inertia::render('Admin/Manage/Branch/Index', [
            'branches' => $branches,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Setting/CreateOrganization',[
            'parent'=>OrganizationConfig::current()
        ]);
    }
}

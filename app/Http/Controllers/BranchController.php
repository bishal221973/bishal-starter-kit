<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $branches = Organization::where('parent_id', 1)->latest()->get();
        $branches1 = Organization::query()
           ->dataTablePaginate($request);
        return Inertia::render('Admin/Manage/Branch/Index', [
            'branches' => $branches,
            'branches1' => $branches1
        ]);
    }
}

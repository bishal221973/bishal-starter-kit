<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index(){
        $branches=Organization::where('parent_id',1)->latest()->get();
        return Inertia::render('Admin/Manage/Branch/Index',[
            'branches'=>$branches
        ]);
    }
}

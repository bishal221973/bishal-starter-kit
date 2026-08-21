<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailVerifyController extends Controller
{
    public function index(){
        return Inertia::render('Auth/VerifyEmail');
    }
}

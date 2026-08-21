<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class EmailVerifyController extends Controller
{
    public function index(){
        return Inertia::render('Auth/VerifyEmail');
    }

    public function send(Request $request){
        // return $request;
        $user=User::first();
        Mail::to($request->email)->send(new WelcomeMail($user));
    }
}

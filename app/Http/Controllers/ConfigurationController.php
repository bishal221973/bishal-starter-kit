<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigurationController extends Controller
{
    public function index(){
        $config=Configuration::first();
        return Inertia::render('Settings/Configuration',[
            'config'=>$config
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SupportAggent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportController extends Controller
{
    public function index()
    {
        return Inertia::render('support');
    }
    public function chat(Request $request)
    {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $response = (new SupportAggent)
            ->prompt($request->message);

        return response()->json([
            'message' => $response->text,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MailSetting;
use Illuminate\Http\Request;

class MailSettingController extends Controller
{
    public function Update(Request $request)
    {
        $data = $request->validate([
            'mailer'       => 'required',
            'host'         => 'required',
            'port'         => 'required',
            'username'     => 'required',
            'password'     => 'required',
            'encryption'   => 'required',
            'from_address' => 'required',
            'from_name'    => 'required',
        ]);

        MailSetting::updateOrCreate(
            ['id' => 1],
            $data
        );

        return redirect()->back()->with('success', "Mail configuration have been changed");
    }
}

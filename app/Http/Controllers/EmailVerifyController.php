<?php

namespace App\Http\Controllers;

use App\Facades\MailConfig;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class EmailVerifyController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/VerifyEmail');
    }

    public function send(Request $request)
    {
        // return $request;
        // $user = User::where('email',$request->email)->first();
        // MailConfig::configure();
        // if($user?->id){
        //     Mail::to($request->email)->send(new WelcomeMail($user));
        //     return redirect()->back();
        // }else{
        //     return redirect()->back()->with('error',"Entered email not found");
        // }

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->with('error', 'Entered email not found.');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email is already verified.');
        }

        MailConfig::configure();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        Mail::to($user->email)->send(
            new WelcomeMail($user, $verificationUrl)
        );

        return back()->with(
            'success',
            'Verification link has been sent to your email.'
        );
    }

    public function verify(Request $request)
    {
        $user = User::findOrFail($request->id);

        if (! hash_equals(
            sha1($user->getEmailForVerification()),
            $request->hash
        )) {
            abort(403, 'Invalid verification link.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()
            ->route('login')
            ->with('success', 'Your email has been verified successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserConfirmationRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            return redirect('/')->with('success', 'You are already logged in.');
        }
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'string|max:100',
            'username' => 'required|string|min:5|max:50|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                Password::min(12)->max(64)->letters()->mixedCase()->numbers()->symbols(),
                'confirmed',
            ],
            'language' => 'required|string|in:en,es,fr,de,it',
            'timezone' => 'required|string|timezone',
            'currency' => 'required|string|in:USD,EUR,GBP',
        ]);

        // Create the user
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'language' => $validatedData['language'],
            'timezone' => $validatedData['timezone'],
            'currency' => $validatedData['currency'],
        ]);

        // Generate a new token
        $token = Str::random(64);

        // Store the token in the database
        $this->saveTheToken($user->email, $token, 'email_verification');

        // Optionally, you can send a confirmation email here
        Mail::to($user->email)->send(new UserConfirmationRequest($user, $token));

        // Log the user in
        // Auth::login($user);

        // Redirect to a success page or perform any other action
        return redirect()->route('home')->with('success', 'User registered successfully. We sent you an e-mail to confirm your account. Please check your inbox.');
    }

    protected function saveTheToken($email, $token, $purpose = 'password_reset')
    {
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email], // Search by email
            [
                'token' => hash('sha256', $token),
                'purpose' => $purpose,
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function verifyEmail(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', hash('sha256', $token))
            ->first();

        if (!$record) {
            return redirect('/')->with('error', 'Invalid or expired verification link.');
        }

        // Mark user as verified
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->save();
        }

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect('login')->with('success', 'Your email has been verified!');
    }

    public function emailNotVerified(Request $request)
    {
        return view('auth.verify-email-new')->with([
            'username' => $request->username,
            'email' => $request->email,
        ]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            // Generate a new token
            $token = Str::random(64);

            // Store the token in the database
            $this->saveTheToken($user->email, $token, 'email_verification');

            // Send the confirmation email again
            Mail::to($user->email)->send(new UserConfirmationRequest($user, $token));

            return redirect()->route('login')->with('success', 'Verification email resent successfully.');
        }

        return redirect()->back()->with('error', 'User not found.');
    }
}

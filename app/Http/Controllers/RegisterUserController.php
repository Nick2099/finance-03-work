<?php

namespace App\Http\Controllers;

use App\Mail\UserConfirmationRequest;
use App\Models\User;
use App\Rules\RegisterUserRules;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
            'first_name' => RegisterUserRules::firstName(),
            'last_name' => RegisterUserRules::lastName(),
            'username' => RegisterUserRules::username(),
            'email' => RegisterUserRules::email(),
            'password' => RegisterUserRules::password(),
            'language' => RegisterUserRules::language(),
            'timezone' => RegisterUserRules::timezone(),
            'currency' => RegisterUserRules::currency(),
            // Remove 'two_factor_auth' and do not validate checkbox as boolean (since it may be absent)
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
            // Use the checkbox field name from the form, default to 0 if not present
            'twofa' => $request->has('two_factor_enabled') ? 1 : 0,
        ]);

        // Generate a new token
        $token = Str::random(64);

        // Store the token in the database
        $this->saveTheToken($user->email, $token, 'email_verification');

        // Optionally, you can send a confirmation email here
        Mail::to($user->email)->send(new UserConfirmationRequest($user, $token));

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
            return redirect('login')->with('error', 'Invalid or expired verification link. You can request a new verification email after submitting correct login credentials in this form.');
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

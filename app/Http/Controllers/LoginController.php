<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetRequest;
use App\Mail\UsernameRecoveryRequest;
use App\Models\User;
use App\Rules\LoginUserRules;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form but only when user is not logged in already.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (Auth::check()) {
            return redirect('/')->with('success', 'You are already logged in.');
        }
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'username' => LoginUserRules::username(),
            'password' => LoginUserRules::password(),
        ]);

        //
        $this->isUserLocked($validatedData['username']);

        // Ensure the user is logged out before attempting to log in
        Auth::logout();

        // Attempt to log in the user with the provided credentials
        if (Auth::attempt($validatedData, $request->remember)) {
            $user = Auth::user();

            if (is_null($user->email_verified_at)) {
                $username = $user->username;
                $email = $user->email;

                Auth::logout();

                return redirect()->route('email-not-verified', [
                    'username' => $username,
                    'email' => $email,
                ]);
            }

            // Reset the wrong login attempts for the user and remove
            // the lock if the user is logged in successfully
            $this->resetWrongLoginAttempts($validatedData['username']);
            // Regenerate the session to prevent session fixation attacks
            // and to ensure the user is logged in with a new session
            request()->session()->regenerate();
            return redirect()->route('home')->with('success', 'Logged in successfully!');
        } else {
            // If login fails, increment the wrong login attempts and lock the user if necessary
            $this->wrongLoginAttempt($validatedData['username']);

            throw ValidationException::withMessages([
                'username' => 'The user with the provided credentials does not exist. ' .
                    'Please check your username and password. ',
            ]);
        };
    }

    /**
     * Handle a logout request to the application.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        Auth::logout();
        return redirect()->route('home')->with('success', 'Logged out successfully!');
    }

    /**
     * Increment the wrong login attempts for the user and lock them if necessary.
     *
     * @param  string  $username
     * @return void
     */
    public function wrongLoginAttempt(string $username)
    {
        $wrongLoginAttempts = DB::table('users')->where('username', $username)->value('wrong_login_attempts');
        // dd($wrongLoginAttempts);
        if (!$wrongLoginAttempts) {
            $wrongLoginAttempts = 1;
        } else {
            $wrongLoginAttempts++;
        }

        $lockMinutes = (int) config('auth.login_lock_minutes');
        $lockedUntil = ($wrongLoginAttempts >= 3) ? Carbon::now()->addMinutes($lockMinutes) : Carbon::now();

        $user = DB::table('users')->where('username', $username)->first();

        if ($user) {
            DB::table('users')->where('username', $username)->update([
                'wrong_login_attempts' => $wrongLoginAttempts,
                'locked_until' => $lockedUntil,
            ]);
        }

        if ($wrongLoginAttempts == 3) {
            throw ValidationException::withMessages([
                'username' => "The provided credentials do not match. Your account is now locked for {$lockMinutes} minute" . ($lockMinutes !== 1 ? 's' : '') . ".",
            ]);
        }
    }

    /**
     * Check if the user is locked out.
     *
     * @param  string  $username
     * @return void
     */
    private function isUserLocked(string $username)
    {
        $user = DB::table('users')->where('username', $username)->first();

        if ($user && $user->locked_until && Carbon::now() < new Carbon($user->locked_until)) {
            $minutes = ceil(Carbon::now()->diffInMinutes(Carbon::parse($user->locked_until)));
            throw ValidationException::withMessages([
                'username' => "Your account is locked for another {$minutes} minute" . ($minutes !== 1 ? 's' : '') . ".",
            ]);
        }
    }

    /**
     * Reset the wrong login attempts for the user.
     *
     * @param  string  $username
     * @return void
     */
    private function resetWrongLoginAttempts(string $username)
    {
        DB::table('users')->where('username', $username)->update([
            'wrong_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Show the password reset form.
     *
     * @return \Illuminate\View\View
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Show the username send form.
     * 
     * @return \Illuminate\View\View
     */
    public function forgotUsername()
    {
        return view('auth.forgot-username');
    }

    /**
     * Handle the username recovery request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function emailUsername(Request $request)
    {
        $validatedData = $request->validate([
            'email' => LoginUserRules::email(),
        ]);

        // Check if the email exists in the database
        // $user = DB::table('users')->where('email', $validatedData['email'])->first();

        $user = User::where('email', $validatedData['email'])->first();

        if ($user) {
            // Send the username to the user's email
            Mail::to($user->email)->send(new UsernameRecoveryRequest($user->username, $user->first_name));
            return redirect()->route('login')->with('success', 'Your username has been sent to your email address.');
        } else {
            return back()->withErrors(['email' => 'The provided email address does not exist.']);
        }
    }

    public function emailPassword(Request $request)
    {
        $validatedData = $request->validate([
            'username' => LoginUserRules::username(),
        ]);

        // Check if the email exists in the database
        $user = User::where('username', $validatedData['username'])->first();

        if ($user) {
            // Generate a new token
            $token = Str::random(64);

            // Store the token in the database
            $this->generate_password_reset_token($user->email, $token, 'password_reset');

            // Send the password reset link to the user's email
            Mail::to($user->email)->send(new PasswordResetRequest($user->email, $token, $user->first_name));
            return redirect()->route('login')->with('success', 'A password reset link has been sent to your email address.');
        } else {
            return back()->withErrors(['email' => 'The provided email address does not exist.']);
        }
    }

    public function resetPassword(Request $request)
    {
        $token = $request->input('token');
        $email = $request->input('email');

        // Verify the token and email
        $record = DB::table('password_reset_tokens')->where('email', $email)->where('token', $token)->first();

        if ($record) {
            return view('auth.reset-password', ['token' => $token, 'email' => $email]);
        } else {
            return redirect()->route('login')->withErrors(['token' => 'Invalid or expired token.']);
        }
    }

    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'password' => LoginUserRules::passwordReset(),
        ]);

        // Verify the token and email
        $record = DB::table('password_reset_tokens')->where('email', $request['email'])->where('token', $request['token'])->first();

        if ($record) {
            // Update the user's password
            User::where('email', $request['email'])->update(['password' => bcrypt($validatedData['password'])]);

            // Delete the token record
            DB::table('password_reset_tokens')->where('email', $request['email'])->delete();

            return redirect()->route('login')->with('success', 'Your password has been reset successfully. You can now log in.');
        } else {
            return redirect()->route('login')->withErrors(['token' => 'Invalid or expired token.']);
        }
    }

    /**
     * Generate a password reset token and store it in the database.
     *
     * @param  string  $email
     * @param  string  $token
     * @param  string  $purpose
     * @return void
     */
    protected function generate_password_reset_token($email, $token, $purpose = 'password_reset')
    {
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email], // Search by email
            [
                'token' => $token,
                'purpose' => $purpose,
                'created_at' => Carbon::now(),
            ]
        );
    }
}

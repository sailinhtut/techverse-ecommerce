<?php

namespace App\Auth\Controllers;

use App\Auth\Models\PasswordResetToken;
use App\Auth\Models\User;
use App\Auth\Services\AuthService;
use App\Auth\Services\UserService;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthController
{
    public function showRegister()
    {
        return view('pages.user.auth.register');
    }

    public function showLogin()
    {
        return view('pages.user.auth.login');
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
                'terms' => 'accepted'
            ]);

            $user = UserService::createUser(
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]
            );

            Auth::login($user);

            return redirect()->route('shop.get');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
            ], [
                'email.required' => 'Email is required',
                'email.email' => 'Email must be valid format',
                'email.exists' => 'Email is not existed',
                'password.required' => 'Password is required'
            ]);

            if (!Auth::attempt($credentials)) {
                throw (new Exception('Incorrect Password'));
            }
            
            // $request->session()->regenerate();
            return redirect('/shop');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('shop.get');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function sendVerificationEmail(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'Email already verified.');
        }

        // Generate temporary signed link
        $verifyUrl = URL::temporarySignedRoute(
            'email.verify.get',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        Mail::send('emails.verify_email', ['url' => $verifyUrl], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify Your Email Address');
        });

        return back()->with('success', 'Verification email sent!');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->email), $hash)) {
            abort(403, 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('profile.get')->with('success', 'Email already verified.');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect()->route('profile.get')->with('success', 'Email verified successfully!');
    }

    public function showForgotPassword()
    {
        return view('pages.user.auth.forgot_password');
    }


    public function sendForgotPasswordEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'Email is required',
                'email.email' => 'Invalid Email Format',
                'email.exists' => 'No user found for this email',
            ]);

            $user = User::where('email', $validated['email'])->firstOrFail();

            $token = PasswordResetToken::generate($validated['email']);

            $signedUrl = URL::temporarySignedRoute(
                'reset-password.get',
                now()->addMinutes(30),
                ['token' => $token->token, 'email' => $validated['email']]
            );


            Mail::send('emails.reset_password', ['url' => $signedUrl], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Reset Password');
            });

            return back()->with('success', 'Reset password email sent!');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function showResetPassword(Request $request)
    {
        try {
            if (!$request->hasValidSignature()) {
                abort(403, 'Invalid or expired reset link.');
            }

            $tokenRecord = PasswordResetToken::where('email', $request->email)
                ->where('token', $request->token)
                ->first();

            if (!$tokenRecord) {
                abort(404, 'Token is invalid');
            }

            if ($tokenRecord->isExpired()) {
                abort(403, 'Token has expired.');
            }

            return view('pages.user.auth.reset_password', ['email' => $request->email, 'token' => $request->token]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
                'token' => 'required|string',
                'password' => 'required|min:6|confirmed',
            ], [
                'password.required' => 'New password is required',
                'password.min' => 'Password length should be at least 6 characters',
                'password.confirmed' => 'Confirm password does not match each other',
                'token.required' => 'Token value is required to identify'
            ]);

            $tokenRecord = PasswordResetToken::where('email', $validated['email'])
                ->where('token', $validated['token'])
                ->first();

            if (!$tokenRecord) {
                abort(404, 'Token is invalid');
            }

            if ($tokenRecord->isExpired()) {
                return back()->withErrors(['token' => 'Token has expired']);
            }

            $user = User::where('email', $validated['email'])->first();
            $user->update(['password' => Hash::make($validated['password'])]);

            $tokenRecord->delete();

            return redirect()->route('login')->with('success', 'Password updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}

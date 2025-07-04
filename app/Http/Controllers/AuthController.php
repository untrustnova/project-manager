<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on user role
            return match($user->role) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'hr' => redirect()->intended(route('hr.dashboard')),
                'employee' => redirect()->intended(route('employee.dashboard')),
                default => redirect()->intended(route('dashboard'))
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => ['nullable', 'string', 'max:500'],
            'phone_number' => ['nullable', 'integer'],
            'birth' => ['nullable', 'date'],
            'telegram_link' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'birth' => $validated['birth'] ?? null,
            'telegram_link' => $validated['telegram_link'] ?? null,
            'role' => 'employee', // Default role
        ]);

        Auth::login($user);

        return redirect()->route('employee.dashboard');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We cannot find a user with that email address.',
            ]);
        }

        // Generate reset token
        $token = Str::random(60);

        // Store token in database (you might want to create a password_resets table)
        // For simplicity, we'll store it in the user's remember_token field temporarily
        $user->update([
            'remember_token' => Hash::make($token),
        ]);

        // In a real application, you would send an email here
        // For now, we'll just redirect with the token (for testing purposes)
        return redirect()->route('password.reset', ['token' => $token, 'email' => $request->email])
            ->with('status', 'Password reset link has been sent! (Check the URL for the reset token)');
    }

    /**
     * Show the reset password form.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle reset password request.
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We cannot find a user with that email address.',
            ]);
        }

        // Verify token (in a real app, you'd check against a password_resets table)
        if (!Hash::check($validated['token'], $user->remember_token)) {
            return back()->withErrors([
                'token' => 'This password reset token is invalid.',
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
            'remember_token' => null, // Clear the token
        ]);

        return redirect()->route('login')
            ->with('status', 'Your password has been reset! You can now login with your new password.');
    }

    /**
     * Show the change password form (for authenticated users).
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Handle change password request (for authenticated users).
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($validated['current_password'], (string) $user->password)) {
            return back()->withErrors([
                'current_password' => 'The provided password does not match your current password.',
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'Your password has been updated successfully!');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(Request $request)
    {
        Log::info('Register attempt', ['email' => $request->input('email')]);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|in:admin,employee,hr',
            'division' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'pendidikan_terakhir' => 'required|string',
            'telegram_link' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string'
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role') ?? 'employee',
            'division' => $request->input('division'),
            'tanggal_masuk' => $request->input('tanggal_masuk'),
            'pendidikan_terakhir' => $request->input('pendidikan_terakhir'),
            'telegram_link' => $request->input('telegram_link'),
            'phone_number' => $request->input('phone_number'),
            'birthdate' => $request->input('birthdate'),
            'address' => $request->input('address'),
            'status' => 'stand_by'
        ]);

        // Generate OTP and send email
        $otp = $user->generateOTP();
        Mail::to($user->email)->send(new OTPMail($otp, $user->name));

        return response()->json([
            'message' => 'User registered successfully. Please check your email for OTP verification.',
            'user' => $user->only(['user_id', 'name', 'email', 'role'])
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        Log::info('Login attempt', ['email' => $request->input('email')]);
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        
        if (!$user->is_verified) {
            return response()->json([
                'message' => 'Please verify your email first.',
                'requires_verification' => true
            ], 422);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(Request $request)
    {
        Log::info('Verify OTP attempt', ['email' => $request->input('email'), 'otp' => $request->input('otp')]);
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !$user->verifyOTP($request->input('otp'))) {
            return response()->json([
                'message' => 'Invalid or expired OTP.'
            ], 422);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Email verified successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        Log::info('Resend OTP attempt', ['email' => $request->input('email')]);
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $otp = $user->generateOTP();
        Mail::to($user->email)->send(new OTPMail($otp, $user->name));

        return response()->json(['message' => 'OTP sent successfully.']);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Log::info('Logout', ['user_id' => $request->user()->user_id ?? null]);
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        Log::info('Profile accessed', ['user_id' => $request->user()->user_id ?? null]);
        return response()->json($request->user());
    }
}
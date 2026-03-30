<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // REGISTER (untuk web/mobile)
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Response sukses
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    // LOGIN (web dan mobile)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if(! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        // Cek apakah request datang dari mobile
        if ($request->expectsJson() || $request->has('device_name')) {
            // MOBILE: Berikan token
            $token = $user->createToken($request->device_name ?? 'mobile')->plainTextToken;
            return response()->json(['token' => $token, 'user' => $user]);
        }

        // WEB: Login biasa via session
        auth()->login($user);
        if ($request->hasSession()) {
        $request->session()->regenerate();
        }
        return response()->json(['message' => 'Login successful', 'user' => $user]);
    }

    //LOGOUT
    public function logout(Request $request) {
        if ($request->expectsJson()) {
            // Mobile: Hapus token yang sedang dipakai
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out from mobile']);
        }

        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out from web']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}

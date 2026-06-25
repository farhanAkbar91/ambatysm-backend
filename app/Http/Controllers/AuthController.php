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

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists and is not public default/external url
            if ($user->profile_picture && !str_starts_with($user->profile_picture, 'http')) {
                // If path starts with /storage/, strip it to get the raw storage relative path
                $storedPath = $user->profile_picture;
                if (str_starts_with($storedPath, '/storage/')) {
                    $storedPath = substr($storedPath, 9);
                }
                \Illuminate\Support\Facades\Storage::disk('public')->delete($storedPath);
            }
            $path = $request->file('profile_picture')->store('profiles', 'public');
            // Store with leading /storage/ so getImageUrl resolves correctly
            $user->profile_picture = '/storage/' . $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; 

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Return user data for SPA
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'is_graduate' => $user->is_graduate,
            ],
            'message' => 'Login successful'
        ]);
    }

    /**
     * Handle token-based authentication without CSRF protection.
     * This method creates a Sanctum token instead of using session authentication.
     */
    public function tokenLogin(Request $request)
    {
        // Validate the input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        // Create a Sanctum token for the user
        $token = $user->createToken('api-token')->plainTextToken;

        // Return user data and token
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'is_graduate' => $user->is_graduate,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Login successful'
        ]);
    }

    /**
     * Revoke the current token (logout for token-based auth).
     */
    public function tokenLogout(Request $request)
    {
        // Delete the current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Token revoked successfully'
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
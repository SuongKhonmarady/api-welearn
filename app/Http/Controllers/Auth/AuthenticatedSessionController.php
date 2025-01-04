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

        $user = User::find(Auth::id()); 

        // Generate a unique token
        $token = Str::random(60); 

        // Update the user's token in the database
        // $user->api_token = Hash::make($token);
        $user->api_token =$token; 
        $user->save();

        // Return no content and set the token in the response headers
        return response()->noContent()->header('Authorization', 'token ' . $token );
        // return response()->json([
        //     'user' => $user,
        //     'token' => $token 
        // ]); 
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
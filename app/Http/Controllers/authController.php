<?php

// app/Http/Controllers/APIAuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\api_token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Delete any existing tokens for this user (single session)
        api_token::where('user_id', $user->id)->delete();

        // Create new token
        $token = bin2hex(random_bytes(32));
        api_token::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->auth_user);
    }

    public function logout(Request $request) 
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
        
        $deleted = api_token::where('token', $token)->delete();
        
        if ($deleted) {
            return response()->json(['message' => 'Logged out successfully']);
        } else {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
}


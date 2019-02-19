<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        if ($token = Auth::attempt($request->toArray())) {
            return $this->respondWithToken($token);
        } else {
            return response()->json(['error' => 'Invalid username or token'], 401);
        }
    }

    public function user()
    {
        return response()->json(Auth::user());
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => "Bearer $token",
        ])->header('Authorization:', "Bearer $token");
    }
}

<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $validatedInput = $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ], [
            'password.required' => 'The token field is required',
        ]);

        if ($token = Auth::setTTL(7200)->attempt($request->toArray())) {
            return $this->respondWithToken($token);
        } else {
            return response()->json(['error' => 'Invalid username or token'], 401);
        }
    }

    public function user()
    {
        return response()->json([
            'result' => true,
            'data' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $validatedInput = $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => 'required',
            'password' => 'sometimes|min:6',
        ]);

        if (isset($validatedInput['password'])) {
            $validatedInput['password'] = Hash::make($validatedInput['password']);
        }

        $user = User::whereId(Auth::id())->update($validatedInput);

        return response()->json(['message' => 'Successfully updated user information', 200]);
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

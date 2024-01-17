<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validation = $request->validate([
            "email"=> "required|email",
            "password"=> "required"
        ]);
        if (!Auth::attempt($validation)) {
            return response()->json(
                ["error"=> "Login field!"],
                401,
            );
        }
        $user = User::where("email", $validation['email'])->first();
        return response()->json([
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer'
        ]);
    }
    public function register(Request $request) {
        $validation = $request->validate([
            'name'=> 'required|max:255',
            'email'=> 'required|email|unique:users,email',
            'password'=> 'required|confirmed|min:6'
        ]);
        $user = User::create($validation);
        return response()->json([
            'data' => $user,
            'access_token'=> $user->createToken('api_token')->plainTextToken,
            'token_type'=> 'Bearer',
        ], 201);
    }

}

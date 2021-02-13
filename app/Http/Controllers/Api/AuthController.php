<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed']
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response()->json([
            'data' => $user,
            'access_token' => $accessToken 
        ]);
    }

    public function login(Request $request )
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if(! auth()->attempt($request->only('email', 'password'))){
            return response(['message' => 'Invalid Credential']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response()->json([
            'data' => auth()->user(),
            'access_token' => $accessToken
        ]);
    }
}

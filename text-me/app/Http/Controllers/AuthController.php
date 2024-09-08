<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);
    

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }


    $user = User::create([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => bcrypt($request->input('password')),
    ]);

    return response()->json([
        'message' => 'You have registered successfully!',
        'user' => new UserResource($user),
    ], 201);

    // $token = $user->createToken('auth_token')->plainTextToken;

    // return response()->json([
    //     'access_token' => $token,
    //     'token_type' => 'Bearer',
    //     'user' => $user,
    // ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if(Auth::attempt(['email'=> $request->input('email'), 'password'=> $request->input('password')])){

            $user = Auth::user();
            $token = $user->createToken('BearerToken')->plainTextToken;

            return response()->json([
                'token'=>$token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ], 200);
        }
        else {
            return response()->json(['message' => 'Login failed'], 401);
        }
    

    
    }

    public function logout()
    {
        
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'You have logged out successfully!'], 200);
    }




}

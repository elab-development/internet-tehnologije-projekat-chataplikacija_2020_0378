<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index(){

        $users = User::all();

        return $users;
    }

    public function show($user_id){

        $user = User::find($user_id);
        if (is_null($user)) {
            return response()->json('Data not found', 404);
        }
        return response()->json($user);

    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', 
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', 
            'email_verified_at' => 'nullable|date', 
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8',
            'is_admin' => 'nullable|boolean', 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        // Hashovanje lozinke pre kreiranja korisnika
        $validatedData = $validator->validated();
        $validatedData['password'] = bcrypt($validatedData['password']);
    
        $user = User::create($validatedData);
    
        return new UserResource($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', 
            'name' => 'required|string|max:255',
            'email' => 'required|email', 
            'email_verified_at' => 'nullable|date', 
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8',
            'is_admin' => 'nullable|boolean', 
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user->update($request->all());

        return new UserResource($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user == null) {
            return response()->json(['error' => 'Data not found'], 404);
        }else {
            $user->delete();

            return response()->json(['User has been deleted'], 200);
        }

        return response()->json('GRESKA', 204);

    }




    //////////////////////////////////////////////

    public function changeUserRole(User $user)
    {
        $user->update(['is_admin' => !(bool)$user->is_admin]);

        $message = "User role was changed into " . ($user->is_admin ? '"Admin"' : '"Regular User"');

        return response()->json(['message' => $message]);
    }

    public function blockUnblock(User $user)
    {
        if ($user->blocked_at) {
            $user->blocked_at = null;
            $message = 'User "'.$user->name.'" has been activated';
        }else {
            $user->blocked_at = now();
            $message = 'User "'.$user->name.'" has been blocked';
        }
        $user->save();

        return response()->json(['message' => $message]);
    }
}

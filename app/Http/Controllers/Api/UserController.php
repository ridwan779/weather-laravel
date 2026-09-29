<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use App\Jobs\SendEmail;

use Validator;
use Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function postRegister(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        SendEmail::dispatch($user);

        return response()->json(['data' => $user, 'token' => $token], 201);
    }

    public function postLogin(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email|max:255',
            'password' => 'required|string|max:255'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is wrong'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['data' => $user, 'token' => $token]);
    }

    public function postLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }

    public function getUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['data' => $user]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __invoke()
    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required',
            'password' => 'required|min:5|confirmed',
            'nama' => 'required',
            'level_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = UserModel::create([
            'username' => request('username'),
            'password' => bcrypt(request('password')),
            'nama' => request('nama'),
            'level_id' => request('level_id'),
        ]);

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Registration failed'
        ], 409);
    }
}

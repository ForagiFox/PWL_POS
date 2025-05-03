<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|min:5|confirmed',
            'nama' => 'required',
            'level_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $user = UserModel::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'nama' => $request->nama,
            'level_id' => $request->level_id,
            'image' => $image->hashName(),
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LevelModel;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function store(Request $request)
    {
        $level = LevelModel::create($request->all());
        return response()->json($level, 201);
    }

    public function show(LevelModel $levelModel)
    {
        return LevelModel::find($levelModel);
    }

    public function update(Request $request, LevelModel $levelModel){
        $levelModel->update($request->all());
        return response()->json($levelModel, 200);
    }

    public function destroy(LevelModel $levelModel)
    {
        $levelModel->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data terhapus'
        ], 200);
    }
}

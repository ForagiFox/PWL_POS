<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index(){
        return BarangModel::all();
    }

    public function show($id)
    {
        $barang = BarangModel::find($id);
        if ($barang) {
            return response()->json($barang, 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Barang Tidak Ada'
            ], 404);
        }
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required',
            'barang_kode' => 'required',
            'barang_nama' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('posts', $image->hashName(), 'public');
        $barang = BarangModel::create([
            'kategori_id' => $request->kategori_id,
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'image' => $image->hashName(),
            ]);
        return response()->json($barang, 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required',
            'barang_kode' => 'required',
            'barang_nama' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $barang = BarangModel::find($id);
        if (!$barang) {
            return response()->json([
                'status' => false,
                'message' => 'Barang Tidak Ada'
            ], 404);
        }
        $image = $request->file('image');
        $barang->update([
            'kategori_id' => $request->kategori_id,
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'image' => $image->hashName(),
        ]);

        if ($request->hasFile('image')) {
            if ($barang->image && Storage::disk('public')->exists('posts/' . $barang->image)) {
                Storage::disk('public')->delete('posts/' . $barang->image);
            }
            $image->storeAs('posts', $image->hashName(), 'public');
        }
        return response()->json($barang, 200);
    }

    public function destroy($id)
    {
        $barang = BarangModel::find($id);
        if (!$barang) {
            return response()->json([
                'status' => false,
                'message' => 'Barang Tidak Ada'
            ], 404);
        }
        if ($barang->image && Storage::disk('public')->exists('posts/' . $barang->image)) {
            Storage::disk('public')->delete('posts/' . $barang->image);
        }
        $barang->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data terhapus'
        ], 200);
    }
}

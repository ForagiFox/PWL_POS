<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenjualanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PenjualanController extends Controller
{
    public function index()
    {
        return PenjualanModel::with(['user', 'detail'])->get();
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'pembeli'          => 'required|string|max:100',
                'penjualan_kode'   => 'required|string|max:20|unique:t_penjualan,penjualan_kode',
                'images'            => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $image = $request->file('images');
            $image->storeAs('posts', $image->hashName(), 'public');
            $penjualan = PenjualanModel::create([
                'pembeli' => $validated['pembeli'],
                'penjualan_kode' => $validated['penjualan_kode'],
                'user_id' => auth()->user()->user_id,
                'penjualan_tanggal' => now(),
                'images' => $image->hashName(),
            ]);

            Log::info('Penjualan baru berhasil dibuat.', ['data' => $penjualan]);

            return response()->json([
                'message' => 'Penjualan berhasil dibuat.',
                'data' => $penjualan
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat membuat penjualan.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        return PenjualanModel::with(['user', 'detail'])->find($id);
    }

    public function update(Request $request, $id)
    {
        $penjualan = PenjualanModel::find($id);
        try {
            $validated = $request->validate([
                'pembeli'          => 'sometimes|string|max:100',
                'penjualan_kode'   => 'sometimes|string|max:20|unique:t_penjualan,penjualan_kode,' . $penjualan->penjualan_id . ',penjualan_id',
                'images'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Handle file upload jika ada
            $image = $request->file('images');
            if ($image) {
                // Hapus file lama jika ada
                if ($penjualan->images) {
                    Storage::disk('public')->delete('posts/' . $penjualan->images);
                }
                // Simpan file baru
                $image->storeAs('posts', $image->hashName(), 'public');
                $validated['images'] = $image->hashName();
            }
            $validated['penjualan_tanggal'] = now();
            $validated['user_id'] = auth()->user()->user_id;
            $penjualan->update([
                'pembeli' => $validated['pembeli'] ?? $penjualan->pembeli,
                'penjualan_kode' => $validated['penjualan_kode'] ?? $penjualan->penjualan_kode,
                'user_id' => $validated['user_id'],
                'penjualan_tanggal' => $validated['penjualan_tanggal'],
                'images' => $image->hashName(),
            ]);

            return response()->json([
                'message' => 'Penjualan berhasil diperbarui.',
                'data' => $penjualan
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Gagal memperbarui penjualan.', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat memperbarui penjualan.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $penjualan = PenjualanModel::find($id);
        if (!$penjualan) {
            return response()->json([
                'error' => 'Penjualan tidak ditemukan.'
            ], 404);
        }

        // Hapus gambar jika ada
        if ($penjualan->images) {
            Storage::disk('public')->delete('posts/' . $penjualan->images);
        }

        // Hapus penjualan
        return $this->deletePenjualan($penjualan);

        try {
            $id = $penjualan->penjualan_id;

            $penjualan->delete();

            Log::warning("Penjualan dengan ID {$id} dihapus.");
            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil dihapus.'
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                Log::error("Gagal menghapus penjualan dengan ID {$penjualan->penjualan_id} karena masih terhubung ke data lain.", [
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'error' => 'Penjualan tidak dapat dihapus karena masih digunakan di transaksi lain.'
                ], 409);
            }

            Log::error("Query exception saat menghapus penjualan.", ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat menghapus penjualan.'
            ], 500);

        } catch (\Exception $e) {
            Log::error("Exception umum saat menghapus penjualan.", ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Terjadi kesalahan tak terduga.'
            ], 500);
        }
    }
}

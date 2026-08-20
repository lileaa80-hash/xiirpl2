<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Exception;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        try {
            $kategori = Kategori::latest()->get();
            return response()->json([
                'status'  => true,
                'message' => 'Data Kategori berhasil diambil',
                'data'    => $kategori,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Perbaikan: ganti kategoris menjadi kategori (sesuai nama tabel)
            $request->validate([
                'nama_kategori' => 'required|unique:kategori,nama_kategori',
            ]);

            $kategori = Kategori::create([
                'nama_kategori' => $request->nama_kategori,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data kategori berhasil dibuat',
                'data'    => $kategori,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kategori = Kategori::find($id);

            if (!$kategori) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Kategori Tidak Ditemukan'
                ], 404);
            }

            $request->validate([
                'nama_kategori' => 'required|string|max:255',
            ]);

            $kategori->update([
                'nama_kategori' => $request->nama_kategori,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Kategori Berhasil Diupdate',
                'data'    => $kategori
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $kategori = Kategori::find($id);
            if (!$kategori) {
                return response()->json(['status' => false, 'message' => 'Data kategori tidak ditemukan'], 404);
            }
            $kategori->delete();
            return response()->json(['status' => true, 'message' => 'Data kategori berhasil dihapus'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
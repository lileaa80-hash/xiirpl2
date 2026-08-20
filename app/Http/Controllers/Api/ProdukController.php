<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * Tampil semua produk (GET /api/produk)
     */
    public function index()
    {
        try {
            // Mengambil produk beserta data relasi kategorinya
            $produk = Produk::with('kategori')->latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'Data produk berhasil diambil.',
                'data'    => $produk
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tambah produk baru (POST /api/produk)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_kategori' => 'required|exists:kategori,id_kategori',
                'nama_produk' => 'required|string|max:255',
                'harga'       => 'required|numeric',
                'stok'        => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $produk = Produk::create([
                'id_kategori' => $request->id_kategori,
                'nama_produk' => $request->nama_produk,
                'harga'       => $request->harga,
                'stok'        => $request->stok,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data produk berhasil ditambahkan.',
                'data'    => $produk->load('kategori')
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail satu produk (GET /api/produk/{id})
     */
    public function show($id)
    {
        try {
            $produk = Produk::with('kategori')->find($id);

            if (!$produk) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data produk tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Detail produk berhasil diambil.',
                'data'    => $produk
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit produk (PUT /api/produk/{id})
     */
    public function update(Request $request, $id)
    {
        try {
            $produk = Produk::find($id);

            if (!$produk) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data produk tidak ditemukan.'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'id_kategori' => 'required|exists:kategori,id_kategori',
                'nama_produk' => 'required|string|max:255',
                'harga'       => 'required|numeric',
                'stok'        => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $produk->update([
                'id_kategori' => $request->id_kategori,
                'nama_produk' => $request->nama_produk,
                'harga'       => $request->harga,
                'stok'        => $request->stok,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data produk berhasil diperbarui.',
                'data'    => $produk->load('kategori')
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus produk (DELETE /api/produk/{id})
     */
    public function destroy($id)
    {
        try {
            $produk = Produk::find($id);

            if (!$produk) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data produk tidak ditemukan.'
                ], 404);
            }

            $produk->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data produk berhasil dihapus.'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
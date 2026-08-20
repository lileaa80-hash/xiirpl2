<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PesananController extends Controller
{
    public function index()
    {
        try {
            $pesanan = Pesanan::with([
                'user',
                'produks'
            ])->get();

            return response()->json([
                'status' => true,
                'message' => 'Data pesanan berhasil diambil.',
                'data' => $pesanan,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'items' => 'required|array',
                'items.*.id_produk' => 'required|exists:produks,id_produk',
                'items.*.jumlah' => 'required|integer|min:1',
            ]);

            $pesanan = new Pesanan;
            // Ambil ID dari user yang sedang login via Sanctum Token
            $pesanan->user_id = $request->user()->id; 
            $pesanan->tanggal = $request->tanggal;
            $pesanan->save();

            $produk = [];
            foreach ($request->items as $item) {
                $produk[$item['id_produk']] = [
                    'jumlah' => $item['jumlah']
                ];
            }

            // Memanggil relasi produks() yang merupakan BelongsToMany
            $pesanan->produks()->attach($produk);

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil ditambahkan.',
                'data' => $pesanan->load('user', 'produks'),
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $pesanan = Pesanan::with([
                'user',
                'produks'
            ])->find($id);

            if (!$pesanan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $pesanan,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pesanan = Pesanan::find($id);

            if (!$pesanan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $request->validate([
                'tanggal' => 'required|date',
                'items' => 'required|array',
                'items.*.id_produk' => 'required|exists:produks,id_produk',
                'items.*.jumlah' => 'required|integer|min:1',
            ]);

            $pesanan->tanggal = $request->tanggal;
            $pesanan->save();

            // Siapkan data produk dan jumlah
            $produk = [];
            foreach ($request->items as $item) {
                $produk[$item['id_produk']] = [
                    'jumlah' => $item['jumlah']
                ];
            }

            // SYNC tabel pivot
            $pesanan->produks()->sync($produk);

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil diperbarui.',
                'data' => $pesanan->load(
                    'user',
                    'produks'
                ),
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $pesanan = Pesanan::find($id);

            if (!$pesanan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            // DETACH dari tabel pivot sebelum hapus pesanan
            $pesanan->produks()->detach();
            $pesanan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil dihapus.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
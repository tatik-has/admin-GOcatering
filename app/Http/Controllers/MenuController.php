<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    // Ambil semua data menu
    public function index(Request $request)
    {
        $query = Menu::query();

        // Cek apakah request punya parameter ?kategori_utama=Kuliner
        if ($request->has('kategori_utama')) {
            $query->byKategoriUtama($request->kategori_utama);
        }

        $menus = $query->get();

        return response()->json([
            'message' => 'Data menu berhasil diambil',
            'data' => $menus
        ]);
    }


    // Ambil detail menu berdasarkan ID
    public function show($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Menu ditemukan',
            'data' => $menu
        ]);
    }

    // Tambah menu baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|string',
            'kategori_utama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'status' => 'required|in:tersedia,habis',
            'porsi' => 'required|integer|min:1',
            'is_featured' => 'required|boolean',
            'is_available' => 'required|boolean',
            'rating' => 'nullable|numeric',
            'estimasi_waktu' => 'nullable|string',
            'catatan_khusus' => 'nullable|string',
            'menu_items' => 'nullable|array', // Kalau kamu pakai JSON array
        ]);

        $menu = Menu::create([
            'nama' => $request->nama,
            'gambar' => $request->gambar,
            'kategori_utama' => $request->kategori_utama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'status' => $request->status,
            'porsi' => $request->porsi,
            'is_featured' => $request->is_featured,
            'is_available' => $request->is_available,
            'rating' => $request->rating,
            'estimasi_waktu' => $request->estimasi_waktu,
            'catatan_khusus' => $request->catatan_khusus,
            'menu_items' => $request->menu_items ? json_encode($request->menu_items) : null,
        ]);

        return response()->json([
            'message' => 'Menu berhasil ditambahkan',
            'data' => $menu
        ], 201);
    }


    // Update menu
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'gambar' => 'nullable|string',
            'kategori' => 'sometimes|required|string',
            'deskripsi' => 'nullable|string',
            'harga' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|in:tersedia,habis',
        ]);

        $menu->update($request->only([
            'nama',
            'gambar',
            'kategori',
            'deskripsi',
            'harga',
            'status'
        ]));

        return response()->json([
            'message' => 'Menu berhasil diperbarui',
            'data' => $menu
        ]);
    }

    // Hapus menu
    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        $menu->delete();

        return response()->json([
            'message' => 'Menu berhasil dihapus'
        ]);
    }
}

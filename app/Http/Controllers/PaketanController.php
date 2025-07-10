<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class PaketanController extends Controller
{
    // Ambil semua data "Paket Bulanan"
    public function index(): JsonResponse
    {
        $data = Menu::byKategoriUtama('paket_bulanan')
            ->available()
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => (string) $menu->id,
                    'name' => $menu->nama,
                    'description' => $menu->deskripsi,
                    'price' => (float) $menu->harga,
                    'rating' => (float) ($menu->rating ?? 4.5),
                    'reviewCount' => 5, // hardcoded sementara
                    'images' => $menu->gambar ? [asset('storage/' . $menu->gambar)] : [],
                    'menuDetails' => array_map(
                        fn($item) => $item['nama_item'] ?? '-',
                        $menu->menu_items ?? []
                    ),
                    'category' => $menu->kategori_utama,
                ];
            });

        return response()->json([
            'message' => 'Data paket bulanan berhasil diambil',
            'data' => $data,
        ]);
    }

    // Ambil detail 1 paket berdasarkan ID
    public function show($id): JsonResponse
    {
        $menu = Menu::byKategoriUtama('paket_bulanan')
            ->available()
            ->find($id);

        if (!$menu) {
            return response()->json([
                'message' => 'Data paket bulanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'id' => (string) $menu->id,
            'name' => $menu->nama,
            'description' => $menu->deskripsi,
            'price' => (float) $menu->harga,
            'rating' => (float) ($menu->rating ?? 4.5),
            'reviewCount' => 5,
            'images' => $menu->gambar ? [asset('storage/' . $menu->gambar)] : [],
            'menuDetails' => array_map(
                fn($item) => $item['nama_item'] ?? '-',
                $menu->menu_items ?? []
            ),
            'category' => $menu->kategori_utama,
        ]);
    }
}

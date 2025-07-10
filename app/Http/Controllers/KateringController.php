<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class KateringController extends Controller
{
    // GET: /api/katering
    public function index(): JsonResponse
    {
        $data = Menu::byKategoriUtama('katering')
            ->available()
            ->get()
            ->map(function ($menu) {
                $menuItems = [];

                if (is_string($menu->menu_items)) {
                    $decoded = json_decode($menu->menu_items, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $menuItems = $decoded;
                    }
                } elseif (is_array($menu->menu_items)) {
                    $menuItems = $menu->menu_items;
                }

                return [
                    'id' => (string) $menu->id,
                    'name' => $menu->nama,
                    'description' => $menu->deskripsi,
                    'price' => (float) $menu->harga,
                    'rating' => (float) ($menu->rating ?? 4.5),
                    'reviewCount' => 5,
                    'images' => $menu->gambar ? [asset('storage/' . $menu->gambar)] : [],
                    'menuDetails' => array_map(
                        fn($item) => $item['nama_item'] ?? '-',
                        $menuItems
                    ),
                    'category' => $menu->kategori_utama,
                ];
            });

        return response()->json([
            'message' => 'Data katering berhasil diambil',
            'data' => $data,
        ]);
    }

    public function show($id): JsonResponse
    {
        try {
            $menu = Menu::byKategoriUtama('katering')
                ->available()
                ->find($id);

            if (!$menu) {
                return response()->json([
                    'message' => 'Data katering tidak ditemukan'
                ], 404);
            }

            \Log::info('menu_items:', [$menu->menu_items]);

            $menuItems = [];

            if (is_string($menu->menu_items)) {
                $decoded = json_decode($menu->menu_items, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $menuItems = $decoded;
                }
            } elseif (is_array($menu->menu_items)) {
                $menuItems = $menu->menu_items;
            }

            return response()->json([
                'id' => (string) $menu->id,
                'name' => $menu->nama,
                'description' => $menu->deskripsi,
                'price' => (float) $menu->harga,
                'rating' => (float) ($menu->rating ?? 4.5),
                'reviewCount' => 5,
                'images' => $menu->gambar ? [asset('storage/' . $menu->gambar)] : [],
                'menuDetails' => collect($menuItems)
                    ->filter(fn($item) => is_array($item))
                    ->map(fn($item) => $item['nama_item'] ?? '-')
                    ->values()
                    ->all(),
                'category' => $menu->kategori_utama,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error in KateringController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
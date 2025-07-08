<?php

namespace App\Filament\Resources\Pemesanan\Pages; // Sesuaikan namespace jika berbeda

use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr; // Diperlukan untuk Arr::get() atau Arr::only()
use App\Models\Pemesanan; // Asumsi Anda memiliki model Pemesanan yang sesuai dengan data API

class ListPemesanan extends ListRecords
{
    protected static string $resource = \App\Filament\Resources\PemesananResource::class; // Pastikan namespace penuh

    public function getTableRecords(): LengthAwarePaginator
    {
        // 1. Ambil data dari API
        $response = Http::get('http://192.168.1.26:8000/api/pesanan');
        $data = $response->json(); // Ini akan menjadi array PHP, misalnya: [[id:1, nama: 'A'], [id:2, nama: 'B']]

        // 2. Konversi data API menjadi koleksi objek "model semu"
        // Ini adalah langkah KRUSIAL agar kolom Filament bisa membaca data
        $itemsAsModels = collect($data)->map(function ($item) {
            // Anda bisa membuat instance model Pemesanan dan mengisi atributnya.
            // Pastikan atribut yang Anda isi sesuai dengan kolom di PemesananResource Anda.
            $model = new Pemesanan(); // Pastikan model Pemesanan bisa di-instantiate tanpa koneksi DB di sini
            $model->fill($item); // Mengisi atribut model dari array item API
            // Jika ada field khusus atau relasi, Anda mungkin perlu mengatur secara manual:
            // $model->id = Arr::get($item, 'id');
            // $model->nama_pelanggan = Arr::get($item, 'nama_field_dari_api');
            // ... dan seterusnya
            return $model;
        });

        // 3. Ambil parameter paginasi dari request Filament
        // Filament akan mengirimkan parameter 'page' dan 'perPage' (limit)
        $page = request()->get('page', 1);
        $perPage = $this->getTableRecordsPerPage() ?? 10; // Ambil dari konfigurasi tabel Filament atau default 10

        // 4. Lakukan paginasi manual pada koleksi
        $paginatedItems = $itemsAsModels->slice(($page - 1) * $perPage, $perPage)->values();

        // 5. Kembalikan LengthAwarePaginator
        return new LengthAwarePaginator(
            $paginatedItems, // Koleksi item yang sudah dipaginasi
            $itemsAsModels->count(), // Total jumlah item (penting untuk informasi total di paginator)
            $perPage, // Item per halaman
            $page, // Halaman saat ini
            ['path' => request()->url()] // Path dasar untuk link paginasi
        );
    }
}
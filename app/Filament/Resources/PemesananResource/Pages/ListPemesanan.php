<?php

namespace App\Filament\Resources\PemesananResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use App\Models\Pemesanan;
use Carbon\Carbon;

class ListPemesanan extends ListRecords
{
    protected static string $resource = \App\Filament\Resources\PemesananResource::class;

    public function getTableRecords(): LengthAwarePaginator
    {
        try {
            // 1. Ambil data dari API
            $response = Http::timeout(30)->get('http://192.168.0.107:8000/api/pesanan');
            
            if (!$response->successful()) {
                throw new \Exception('API request failed');
            }
            
            $data = $response->json();

            // 2. Konversi array JSON ke koleksi model semu
            $itemsAsModels = collect($data)->map(function ($item) {
                $model = new Pemesanan();

                // Isi atribut dasar
                $model->id = Arr::get($item, 'id');
                $model->nama_pelanggan = Arr::get($item, 'nama_pelanggan');
                $model->pesanan = Arr::get($item, 'pesanan', '-'); // Nama menu yang dipesan
                $model->jumlah = Arr::get($item, 'jumlah');
                $model->total_harga = Arr::get($item, 'total');
                $model->alamat = Arr::get($item, 'alamat');
                $model->telepon = Arr::get($item, 'no_hp');
                $model->status = Arr::get($item, 'status');
                
                // Pastikan request tidak null dan tampilkan dengan benar
                $model->request = Arr::get($item, 'request') ?? '-';
                
                // Konversi created_at ke timezone Asia/Jakarta dengan benar
                $createdAt = Arr::get($item, 'created_at');
                if ($createdAt) {
                    $model->created_at = Carbon::parse($createdAt)->setTimezone('Asia/Jakarta');
                } else {
                    $model->created_at = Carbon::now('Asia/Jakarta');
                }

                // Set tanggal_pesan sesuai dengan created_at
                $model->tanggal_pesan = $model->created_at->format('Y-m-d');

                return $model;
            });

            // 3. Setup pagination manual
            $page = request()->get('page', 1);
            $perPage = $this->getTableRecordsPerPage() ?? 10;

            $paginatedItems = $itemsAsModels->slice(($page - 1) * $perPage, $perPage)->values();

            return new LengthAwarePaginator(
                $paginatedItems,
                $itemsAsModels->count(),
                $perPage,
                $page,
                ['path' => request()->url()]
            );

        } catch (\Exception $e) {
            // Jika API gagal, return empty paginator
            return new LengthAwarePaginator(
                collect([]),
                0,
                10,
                1,
                ['path' => request()->url()]
            );
        }
    }
}
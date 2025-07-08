<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\paketan;

class PaketanController extends Controller
{
    // Ambil semua data paketan
    public function index()
    {
        $data = paketan::all();

        return response()->json([
            'message' => 'Data paketan berhasil diambil',
            'data' => $data
        ]);
    }

    // Ambil paketan berdasarkan ID
    public function show($id)
    {
        $paketan = Paketan::find($id);

        if (!$paketan) {
            return response()->json([
                'message' => 'Data paketan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data paketan ditemukan',
            'data' => $paketan
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PemesananController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_phone' => 'required|string|max:20',
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|integer|exists:menus,id',
            'items.*.menu_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'delivery_address' => 'required|string|max:500',
            'request_note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            if (!Auth::check()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            $userId = Auth::id();

            $order = Order::create([
                'user_id' => $userId,
                'customer_name' => Auth::user()->name,
                'customer_phone' => $request->customer_phone,
                'items' => $request->items,
                'request_note' => $request->request_note,
                'total_amount' => $request->total_amount,
                'status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'created_at' => Carbon::now('Asia/Jakarta'),
                'updated_at' => Carbon::now('Asia/Jakarta'),
            ]);

            return response()->json(['message' => 'Pesanan berhasil dibuat.', 'order' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat pesanan.', 'error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $userId = Auth::id();
        $perPage = $request->input('per_page', 10);
        $statusFilter = $request->input('status');

        $orders = Order::where('user_id', $userId)
            ->when($statusFilter, function ($query, $statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $formattedOrders = $orders->getCollection()->map(function ($order) {
            $pesananDetail = [];
            $jumlahDetail = [];

            foreach ($order->items as $item) {
                $menuName = $item['menu_name'] ?? 'Menu Tidak Dikenal';
                $quantity = $item['quantity'] ?? 0;
                $unit = $item['unit'] ?? 'porsi';
                $pesananDetail[] = $menuName;
                $jumlahDetail[] = $quantity . ' ' . $unit;
            }

            return [
                'id' => (int) $order->id, // PERBAIKAN: Pastikan ID adalah integer
                'order_id' => (int) $order->id, // PERBAIKAN: Konsisten dengan naming dan type
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'items_summary' => implode(' + ', $pesananDetail),
                'items_quantities' => implode(', ', $jumlahDetail),
                'detailed_items' => $order->items,
                'request_note' => $order->request_note ?? '-',
                'total_amount' => (float) $order->total_amount,
                'total_amount_formatted' => 'Rp. ' . number_format($order->total_amount, 0, ',', '.'),
                'order_date' => Carbon::parse($order->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i'),
                'delivery_address' => $order->delivery_address,
                'status' => $order->status,
                'created_at' => $order->created_at->timezone('Asia/Jakarta')->toDateTimeString(),
                'updated_at' => $order->updated_at->timezone('Asia/Jakarta')->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $formattedOrders,
            'pagination' => [
                'current_page' => (int) $orders->currentPage(), // PERBAIKAN: Cast ke int
                'from' => (int) $orders->firstItem(),
                'last_page' => (int) $orders->lastPage(),
                'per_page' => (int) $orders->perPage(),
                'to' => (int) $orders->lastItem(),
                'total' => (int) $orders->total(),
                'next_page_url' => $orders->nextPageUrl(),
                'prev_page_url' => $orders->previousPageUrl(),
            ]
        ]);
    }

    // Method khusus untuk Filament Admin - endpoint /api/pesanan
    public function getAllForAdmin(Request $request)
    {
        try {
            $orders = Order::with(['user'])
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedOrders = $orders->map(function ($order) {
                $pesananDetail = [];
                $jumlahDetail = [];

                foreach ($order->items as $item) {
                    $menuName = $item['menu_name'] ?? 'Menu Tidak Dikenal';
                    $quantity = $item['quantity'] ?? 0;
                    $unit = $item['unit'] ?? 'porsi';
                    $pesananDetail[] = $menuName;
                    $jumlahDetail[] = $quantity . ' ' . $unit;
                }

                return [
                    'id' => (int) $order->id, // PERBAIKAN: Cast ke int
                    'nama_pelanggan' => $order->customer_name,
                    'pesanan' => implode(' + ', $pesananDetail),
                    'jumlah' => implode(', ', $jumlahDetail),
                    'total' => (float) $order->total_amount,
                    'alamat' => $order->delivery_address,
                    'no_hp' => $order->customer_phone,
                    'request' => $order->request_note ?? '-',
                    'status' => $order->status,
                    'created_at' => $order->created_at->timezone('Asia/Jakarta')->toDateTimeString(),
                    'updated_at' => $order->updated_at->timezone('Asia/Jakarta')->toDateTimeString(),
                    'user' => [
                        'id' => (int) $order->user_id, // PERBAIKAN: Cast ke int
                        'name' => $order->user->name ?? 'Unknown User'
                    ]
                ];
            });

            return response()->json($formattedOrders);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil data pesanan.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return response()->json(['message' => 'Akses ditolak. Pesanan bukan milik Anda.'], 403);
        }

        $pesananDetail = [];
        $jumlahDetail = [];

        foreach ($order->items as $item) {
            $menuName = $item['menu_name'] ?? 'Menu Tidak Dikenal';
            $quantity = $item['quantity'] ?? 0;
            $unit = $item['unit'] ?? 'porsi';
            $pesananDetail[] = $menuName;
            $jumlahDetail[] = $quantity . ' ' . $unit;
        }

        $formattedOrder = [
            'id' => (int) $order->id, // PERBAIKAN: Cast ke int
            'order_id' => (int) $order->id, // PERBAIKAN: Konsisten dengan naming dan type
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'items_summary' => implode(' + ', $pesananDetail),
            'items_quantities' => implode(', ', $jumlahDetail),
            'detailed_items' => $order->items,
            'request_note' => $order->request_note ?? '-',
            'total_amount' => (float) $order->total_amount,
            'total_amount_formatted' => 'Rp. ' . number_format($order->total_amount, 0, ',', '.'),
            'order_date' => Carbon::parse($order->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i'),
            'delivery_address' => $order->delivery_address,
            'status' => $order->status,
            'created_at' => $order->created_at->timezone('Asia/Jakarta')->toDateTimeString(),
            'updated_at' => $order->updated_at->timezone('Asia/Jakarta')->toDateTimeString(),
        ];

        return response()->json($formattedOrder);
    }
}
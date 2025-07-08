<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class pemesananController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required_without:user_id|string|max:255',
            'customer_phone' => 'required_without:user_id|string|max:20',
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
            // Gunakan Auth::check() agar tidak error jika user tidak login
            $userId = Auth::check() ? Auth::id() : null;

            $order = Order::create([
                'user_id' => $userId,
                'customer_name' => $userId ? Auth::user()->name : $request->customer_name,
                'customer_phone' => $userId
                    ? (Auth::user()->phone_number ?? $request->customer_phone)
                    : $request->customer_phone,
                'items' => $request->items,
                'request_note' => $request->request_note,
                'total_amount' => $request->total_amount,
                'status' => 'pending',
                'delivery_address' => $request->delivery_address,
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
                'order_id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'items_summary' => implode(' + ', $pesananDetail),
                'items_quantities' => implode(', ', $jumlahDetail),
                'detailed_items' => $order->items,
                'request_note' => $order->request_note ?? '-',
                'total_amount' => (float) $order->total_amount,
                'total_amount_formatted' => 'Rp. ' . number_format($order->total_amount, 0, ',', '.'),
                'order_date' => Carbon::parse($order->created_at)->translatedFormat('d F Y H:i'),
                'delivery_address' => $order->delivery_address,
                'status' => $order->status,
                'created_at' => $order->created_at->toDateTimeString(),
                'updated_at' => $order->updated_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $formattedOrders,
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'from' => $orders->firstItem(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'to' => $orders->lastItem(),
                'total' => $orders->total(),
                'next_page_url' => $orders->nextPageUrl(),
                'prev_page_url' => $orders->previousPageUrl(),
            ]
        ]);
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
            'order_id' => $order->id,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'items_summary' => implode(' + ', $pesananDetail),
            'items_quantities' => implode(', ', $jumlahDetail),
            'detailed_items' => $order->items,
            'request_note' => $order->request_note ?? '-',
            'total_amount' => (float) $order->total_amount,
            'total_amount_formatted' => 'Rp. ' . number_format($order->total_amount, 0, ',', '.'),
            'order_date' => Carbon::parse($order->created_at)->translatedFormat('d F Y H:i'),
            'delivery_address' => $order->delivery_address,
            'status' => $order->status,
            'created_at' => $order->created_at->toDateTimeString(),
            'updated_at' => $order->updated_at->toDateTimeString(),
        ];

        return response()->json($formattedOrder);
    }
}

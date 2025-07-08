<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon; // Pastikan Carbon di-import

class OrderController extends Controller
{
    /**
     * Get a list of orders for the admin dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $statusFilter = $request->input('status'); // Untuk filter berdasarkan status

        $orders = Order::query()
            ->with('user') // Eager load user jika relasi ada
            ->when($search, function ($query, $search) {
                $query->where('customer_name', 'like', '%' . $search . '%')
                      ->orWhere('delivery_address', 'like', '%' . $search . '%')
                      ->orWhere('total_amount', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($q) use ($search) { // Cari juga di nama user
                          $q->where('name', 'like', '%' . $search . '%');
                      })
                      ->orWhereJsonContains('items', ['menu_name' => $search]); // Cari di nama menu dalam JSON items
            })
            ->when($statusFilter, function ($query, $statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Transformasi data agar sesuai dengan kebutuhan frontend
        $formattedOrders = $orders->getCollection()->map(function ($order) {
            $customerName = $order->customer_name ?? ($order->user->name ?? 'N/A');
            $customerPhone = $order->customer_phone ?? ($order->user->phone_number ?? 'N/A');

            // Menggabungkan nama menu dan jumlahnya
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
                'id'               => $order->id,
                'customer_name'    => $customerName,
                'customer_phone'   => $customerPhone,
                'pesanan'          => implode(' + ', $pesananDetail), // Gabungkan semua nama menu
                'jumlah'           => implode(', ', $jumlahDetail),    // Gabungkan semua jumlah
                'request_note'     => $order->request_note ?? '-',
                'total_amount'     => 'Rp. ' . number_format($order->total_amount, 0, ',', '.'),
                'order_date'       => Carbon::parse($order->created_at)->translatedFormat('d F Y'),
                'delivery_address' => $order->delivery_address,
                'status'           => $order->status,
                'is_delivered'     => $order->status === 'delivered' || $order->status === 'completed', // Status untuk aksi di frontend
                'created_at'       => $order->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $formattedOrders,
            'links' => [
                'first' => $orders->url(1),
                'last'  => $orders->url($orders->lastPage()),
                'prev'  => $orders->previousPageUrl(),
                'next'  => $orders->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $orders->currentPage(),
                'from'         => $orders->firstItem(),
                'last_page'    => $orders->lastPage(),
                'path'         => $orders->path(),
                'per_page'     => $orders->perPage(),
                'to'           => $orders->lastItem(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    /**
     * Update the status of an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,delivered',
        ]);

        $order->status = $request->status;
        // if ($request->status === 'delivered') {
        //     $order->delivered_at = Carbon::now();
        // } else {
        //     $order->delivered_at = null;
        // }
        $order->save();

        return response()->json(['message' => 'Order status updated successfully.', 'order' => $order->fresh()]); // Mengembalikan data order terbaru
    }

    // Metode lain (show, delete) bisa ditambahkan jika perlu
}
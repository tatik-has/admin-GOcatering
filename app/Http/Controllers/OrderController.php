<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $statusFilter = $request->input('status');

        $orders = Order::query()
            ->with('user')
            ->when($search, function ($query, $search) {
                $query->where('customer_name', 'like', '%' . $search . '%')
                      ->orWhere('delivery_address', 'like', '%' . $search . '%')
                      ->orWhere('total_amount', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      })
                      ->orWhereJsonContains('items', ['menu_name' => $search]);
            })
            ->when($statusFilter, function ($query, $statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $formattedOrders = $orders->getCollection()->map(function ($order) {
            $customerName = $order->customer_name ?? ($order->user->name ?? 'N/A');
            $customerPhone = $order->customer_phone ?? ($order->user->phone_number ?? 'N/A');

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
                'id'               => (int) $order->id, // PERBAIKAN: Cast ke int
                'customer_name'    => $customerName,
                'customer_phone'   => $customerPhone,
                'pesanan'          => implode(' + ', $pesananDetail),
                'jumlah'           => implode(', ', $jumlahDetail),
                'request_note'     => $order->request_note ?? '-',
                'total_amount'     => 'Rp. ' . number_format($order->total_amount, 0, ',', '.'),
                'order_date'       => Carbon::parse($order->created_at)->translatedFormat('d F Y'),
                'delivery_address' => $order->delivery_address,
                'status'           => $order->status,
                'is_delivered'     => $order->status === 'delivered' || $order->status === 'completed',
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
                'current_page' => (int) $orders->currentPage(), // PERBAIKAN: Cast ke int
                'from'         => (int) $orders->firstItem(),
                'last_page'    => (int) $orders->lastPage(),
                'path'         => $orders->path(),
                'per_page'     => (int) $orders->perPage(),
                'to'           => (int) $orders->lastItem(),
                'total'        => (int) $orders->total(),
            ],
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,delivered',
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => $order->fresh()
        ]);
    }
}
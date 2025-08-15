<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food_Model;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class Orders_Contoller extends Controller
{
    public function store(Request $request)
    {
        $today = now()->toDateString();
        log::info($request->all());
        // หาเลขคิวสูงสุดของวันนี้
        $lastOrder = Order::whereDate('created_at', $today)
            ->orderBy('queue_number', 'desc')
            ->first();

        $queueNumber = $lastOrder ? $lastOrder->queue_number + 1 : 1;
        $queueCode = 'Q' . now()->format('Ymd') . '-' . str_pad($queueNumber, 3, '0', STR_PAD_LEFT);

        $order = Order::create([
            'table_number' => $request->table_number,
            'queue_number' => $queueNumber,
            'queue_code' => $queueCode,
            'status_queue' => 'รับออเดอร์',
            'status_payment' => 'ยังไม่จ่าย',
            'total_price' => $request->total_price,
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => Food_Model::find($item['id'])->price,
            ]);
        }

        return response()->json(['success' => true, 'data' => $order]);
    }
    public function getData()
    {
        // ดึง orders พร้อม items และ product ของแต่ละ item
        $today = now()->toDateString();
        $orders = Order::with('items.product')
           ->whereDate('created_at', $today)

        ->orderBy('queue_number')->get();

        // แปลงข้อมูลให้เหมือน mock data
        $orders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'table_number' => $order->table_number,
                'queue_code' => $order->queue_code,
                'queue_number' => $order->queue_number,
                'status_queue' => $order->status_queue,
                'status_payment' => $order->status_payment,
                'total_price' => $order->total_price,
                'created_at' => $order->created_at,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'order_id' => $item->order_id,
                        'product_id' => $item->product_id,
                        // ใช้ชื่อสินค้าจาก product
                        'product_name' => $item->product ? $item->product->name : null,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                })
            ];
        });

        return response()->json(['data' => $orders]);
    }

    // อัพเดทสถานะออเดอร์
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_queue' => 'required|string'
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->status_queue = $request->status_queue;
        $order->save();

        return response()->json([
            'message' => 'เปลี่ยนสถานะออเดอร์เรียบร้อยเเล้ว',
            'status_queue' => $order->status_queue
        ]);

    }

}

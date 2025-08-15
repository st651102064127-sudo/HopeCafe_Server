<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Payment_Contoller extends Controller
{
    function getOrders(Request $req)
    {
        log::info($req->all());
        $table = Order::where('table_number', $req->table)
            ->where('status_payment', operator: 'ยังไม่จ่าย')
            ->with(['orderItems.product'])
            ->get();

        if (!$table) {
            return response()->json([
                'message' => ' ไม่รายการอาหารของโต๊ะ' . $req->table,
            ]);
        }
        return response()->json(data: $table);
    }
    function updatePayment(Request $request)
    {
        $request->validate([
            'table_number' => 'required',
        ]);

        // หาออร์เดอร์ของโต๊ะที่ยังไม่จ่าย
        $orders = Order::where('table_number', $request->table_number)
            ->where('status_payment', 'ยังไม่จ่าย')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'ไม่พบรายการอาหารที่ต้องชำระของโต๊ะ ' . $request->table_number,
            ], 404);
        }

        // อัปเดตสถานะการชำระเงินและเวลาปรับปรุง
        foreach ($orders as $order) {
            $order->status_payment = 'จ่ายแล้ว';
            $order->save();
        }

        return response()->json([
            'message' => 'อัปเดตการชำระเงินเรียบร้อย',
            'table_number' => $request->table_number,
        ]);
    }
}

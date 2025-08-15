<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories_Model;
use App\Models\Food_Model;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{
    // ข้อมูลสรุป
    public function summary()
    {
        // ยอดขายรวมทั้งหมด
        $totalSales = Order::where('status_payment', 'จ่ายแล้ว')->sum('total_price');
      log::info($totalSales);
        // ยอดขายวันนี้
        $dailySales = Order::whereDate('created_at', now())
            ->where('status_payment', 'จ่ายแล้ว')
            ->sum('total_price');

        // ยอดขายสัปดาห์นี้ (7 วันล่าสุด)
        $weeklySales = Order::where('status_payment', 'จ่ายแล้ว')
            ->whereBetween('created_at', [now()->subDays(6), now()])
            ->sum('total_price');

        // เมนูที่สั่งเยอะที่สุด 5 อันดับ
       $topMenu = OrderItem::select(
        'order_items.product_id',
        DB::raw('SUM(order_items.quantity) as total_quantity'),
        'food.name as food_name'
    )
    ->join('food', DB::raw('order_items.product_id COLLATE utf8mb4_0900_ai_ci'), '=', DB::raw('food.id COLLATE utf8mb4_0900_ai_ci'))
    ->groupBy('order_items.product_id', 'food.name')
    ->orderByDesc('total_quantity')
    ->limit(5)
    ->get();

        // จำนวนหมวดหมู่และรายการอาหาร
        $categoryCount = Categories_Model::count();
        $foodCount = Food_Model::count();

        // จำนวนผู้ใช้งาน
        $adminCount = User::where('role', 'admin')->count();
        $staffCount = User::where('role', 'staff')->count();

        return response()->json([
            'totalSales' => $totalSales,
            'dailySales' => $dailySales,
            'weeklySales' => $weeklySales,
            'topMenu' => $topMenu,
            'categoryCount' => $categoryCount,
            'foodCount' => $foodCount,
            'adminCount' => $adminCount,
            'staffCount' => $staffCount,
        ]);
    }

    // ยอดขายรายวันย้อนหลัง 7 วัน
    public function dailySales()
    {
        $data = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->where('status_payment', 'จ่ายแล้ว')
            ->whereBetween('created_at', [now()->subDays(6), now()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        return response()->json(['data' => $data]);
    }

    // ยอดขายรายสัปดาห์ในเดือนที่ผ่านมา
    public function weeklySales()
    {
        $data = Order::select(DB::raw('WEEK(created_at,1) as week'), DB::raw('SUM(total_price) as total'))
            ->where('status_payment', 'จ่ายแล้ว')
            ->whereBetween('created_at', [now()->subMonth(), now()])
            ->groupBy(DB::raw('WEEK(created_at,1)'))
            ->orderBy('week')
            ->get();
        return response()->json(['data' => $data]);
    }

    // ยอดขายรายเดือนในปีนี้
    public function monthlySales()
    {
        $data = Order::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_price) as total'))
            ->where('status_payment', 'จ่ายแล้ว')
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();
        return response()->json(['data' => $data]);
    }
}

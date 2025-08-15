<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_number',
        'queue_code',
        'queue_number',
        'status_queue',
        'status_payment',
        'total_price',
    ];

    // ความสัมพันธ์: ออเดอร์มีหลายรายการสินค้า
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ฟังก์ชันสร้างเลขคิวอัตโนมัติ (reset ทุกวัน)
    public static function generateQueue()
    {
        $today = now()->format('Y-m-d');
        $lastOrder = self::whereDate('created_at', $today)
            ->orderBy('queue_number', 'desc')
            ->first();

        $nextQueueNumber = $lastOrder ? $lastOrder->queue_number + 1 : 1;
        $queueCode = 'Q' . now()->format('Ymd') . '-' . str_pad($nextQueueNumber, 3, '0', STR_PAD_LEFT);

        return [
            'queue_number' => $nextQueueNumber,
            'queue_code' => $queueCode,
        ];
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }


}

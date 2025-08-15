<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Food_Model;
use App\Models\Order;
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    // ความสัมพันธ์กับออเดอร์
    public function order()
    {
        return $this->belongsTo(Order::class,'table_number','table_number');
    }

    // ความสัมพันธ์กับสินค้า
    public function product()
    {
        return $this->belongsTo(Food_Model::class, 'product_id', 'id');
    }
       public function food() {
        return $this->belongsTo(Food_Model::class, 'product_id', 'id');
    }

}

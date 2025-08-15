<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food_Model extends Model
{
    protected $table = "food";
    protected $primaryKey = 'id';
    protected $keyType = 'string'; // ถ้า id เป็น string UUID
    public $incrementing = false; // ถ้า id ไม่ใช่ออโต้

    protected $fillable = [
        'id',           // id ควรอยู่บนสุด
        'name',
        'description',
        'price',
        'category_id',
        'image',
    ];

}

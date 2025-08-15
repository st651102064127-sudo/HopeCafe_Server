<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories_Model extends Model
{
    protected $table = 'categories';  // บอก Laravel ว่าใช้ตารางนี้

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'description'];

    public $timestamps = true;
}

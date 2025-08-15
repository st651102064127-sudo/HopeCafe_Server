<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // ใช้ Authenticatable เพราะเป็นตาราง users
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee_Model extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * กำหนดฟิลด์ที่สามารถกรอกข้อมูลได้
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'status',
    ];

    /**
     * ฟิลด์ที่ไม่ต้องการให้แสดงเวลาแปลงเป็น array/json
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * แปลงชนิดข้อมูลอัตโนมัติ
     */
    protected $casts = [
        'status' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Mutator เข้ารหัส password อัตโนมัติเมื่อบันทึก
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}

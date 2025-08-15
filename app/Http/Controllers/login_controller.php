<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Employee_Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class login_controller extends Controller
{



    public function login(Request $request)
    {
        // Validate
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ], [
            'phone.required' => 'กรุณากรอกเบอร์โทร',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
        ]);

        try {
            // หา user ด้วย phone
            $user = Employee_Model::where('phone', $request->phone)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่พบบัญชีผู้ใช้'
                ], 404);
            }

            // ตรวจสอบ password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'รหัสผ่านไม่ถูกต้อง'
                ], 401);
            }

            // ตรวจสอบสถานะ active
            if (!$user->status) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'บัญชีถูกระงับ'
                ], 403);
            }

            // ✅ สร้าง token ด้วย Laravel Sanctum
            $token = $user->createToken('web_token')->plainTextToken;

            // ส่งกลับ frontend
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => $user,
            ], 200);

        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดฝั่ง server ระหว่างการ login',
                'errors' => $err->getMessage()
            ], 500);
        }
    }

   public function getProfile(Request $request) {
    // auth:sanctum จะ attach user ให้
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 401);
    }

    return response()->json([
        'status' => 'success',
        'user' => $user
    ]);
}




}

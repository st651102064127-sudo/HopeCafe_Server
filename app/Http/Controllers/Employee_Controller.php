<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Employee_Model;
class Employee_Controller extends Controller
{
    function Store(Request $req)
    {
        try {
            $req->validate([
                'name' => 'required|string|max:255|unique:users,name',
                'password' => 'required|string|min:6',
                'role' => 'required',
                'phone' => 'nullable|string|max:20|unique:users,phone',

                'status' => 'required|boolean',
            ], [
                'name.required' => 'กรุณากรอกชื่อพนักงาน',
                'name.string' => 'ชื่อพนักงานต้องเป็นตัวอักษร',
                'name.max' => 'ชื่อพนักงานต้องไม่เกิน 255 ตัวอักษร',
                'name.unique' => 'มีชื่อนี้อยู่แล้ว',



                'password.required' => 'กรุณากรอกรหัสผ่าน',
                'password.string' => 'รหัสผ่านต้องเป็นตัวอักษร',
                'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',

                'role.required' => 'กรุณาเลือกตำแหน่งพนักงาน',

                'phone.string' => 'เบอร์โทรต้องเป็นตัวอักษร',
                'phone.max' => 'เบอร์โทรต้องไม่เกิน 20 ตัวอักษร',
                'phone.unique' => 'มีเบอร์นี้อยู่แล้ว',
                'status.required' => 'กรุณาเลือกสถานะ',
                'status.boolean' => 'สถานะต้องเป็น Active หรือ Inactive',
            ]);
            $data = $req->all();
            Employee_Model::create($data);
            return response()->json([
                'message' => 'เพิ่มข้อมูลพนักงานสำเร็จ',
                'status' => 'success',
                'data' => $data
            ]);
        } catch (err) {
            return response()->json([
                'message' => 'เกิดข้อผิดพลาดฝั่ง Server',
                'status' => 'error',
            ], 500);
        }
    }

    function fetdata()
    {
        try {
            $data = Employee_Model::get();
            if ($data) {
                return response()->json([
                    'message' => 'เรียกข้อมูลสำเร็จ',
                    'data' => $data
                ], 200);
            }
            return response()->json([
                'message' => 'ไม่มีข้อมูลในระบบ',

            ], 200);
        } catch (err) {
            return response()->json([
                'message' => 'เกิดข้อผิดพลาดฝั่ง Server',
                'status' => 'error',
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $employee = Employee_Model::find($id);
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่พบพนักงาน',
                ], 404);
            }

            // Validate ข้อมูลที่ส่งมา
            $request->validate([
                'name' => 'required|string|max:255',
                'role' => 'required|in:admin,staff',
                'phone' => 'nullable|string|max:20',
                'status' => 'required|boolean',
            ], [
                'name.required' => 'กรุณากรอกชื่อพนักงาน',
                'role.required' => 'กรุณาเลือกตำแหน่ง',
                'role.in' => 'ตำแหน่งต้องเป็น admin หรือ staff',
                'phone.max' => 'เบอร์โทรต้องไม่เกิน 20 ตัวอักษร',
                'status.required' => 'กรุณาเลือกสถานะ',
                'status.boolean' => 'สถานะต้องเป็น true หรือ false',
            ]);

            // อัปเดทข้อมูล
            $employee->name = $request->name;
            $employee->role = $request->role;
            $employee->phone = $request->phone;
            if($request->password){
                $employee->password = $request->password;
            }
            $employee->status = $request->status;
            $employee->save();

            // ส่ง response กลับ
            return response()->json([
                'status' => 'success',
                'message' => 'อัปเดตพนักงานเรียบร้อยแล้ว',
                'employee' => $employee,
            ], 200);

        } catch (err) {
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดฝั่ง server',

            ], 500);
        }
    }
public function delete($id){
 try {
        // หา employee ตาม id
        $employee = Employee_Model::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'ไม่พบข้อมูลนี้',
                'status' => 'error'
            ], 404);
        }

        // ลบข้อมูล
        $employee->delete();

        return response()->json([
            'message' => 'ลบข้อมูลเสร็จสิ้น',
            'status' => 'success'
        ], 200);

    } catch (\Exception $err) {
        return response()->json([
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาดฝั่ง server',
            'errors' => $err->getMessage()
        ], 500);
    }
}
}

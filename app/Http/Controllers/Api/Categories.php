<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories_Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class Categories extends Controller
{
    public function store(Request $req)
    {
        try {
            $req->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
            ], [
                'name.required' => 'กรุณากรอกชื่อหมวดหมู่',
                'name.unique' => 'ชื่อหมวดหมู่นี้มีอยู่แล้ว',
                'name.max' => 'ชื่อหมวดหมู่ต้องไม่เกิน 255 ตัวอักษร',
                'description.string' => 'คำอธิบายต้องเป็นข้อความ',
            ]);

            $data = [
                'name' => $req->name,
                'description' => $req->description,
                'id' => Str::uuid()->toString(),
            ];
            $category = Categories_Model::create($data);
            return response()->json([
                'status' => 'success',
                'message' => 'เพิ่มหมวดหมู่สำเร็จ',
                'data' => $category,
            ]);
        } catch (err) {

            return response()->json([
                'status' => 'error',
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'data' => $data

            ], 422);
        }
    }
public function index()
{
    try {
        $categories = Categories_Model::all();
        return response()->json([
            'data' => $categories,
        ]);
    } catch (\Throwable $e) { // แก้ไขตรงนี้
        return response()->json([
            'status' => 'error',
            'message' => 'ข้อมูลไม่ถูกต้อง',
            // 'error' => $e->getMessage(), // แนะนำให้เพิ่มบรรทัดนี้เพื่อดูข้อผิดพลาดที่แท้จริง
        ], 422);
    }
}
    public function update(Request $req, $id)
    {
        try {
            $req->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
            ], [
                'name.required' => 'กรุณากรอกชื่อหมวดหมู่',
                'name.unique' => 'ชื่อหมวดหมู่นี้มีอยู่แล้ว',
                'name.max' => 'ชื่อหมวดหมู่ต้องไม่เกิน 255 ตัวอักษร',
                'description.string' => 'คำอธิบายต้องเป็นข้อความ',
            ]);

            $category = Categories_Model::findOrFail($id);
            $category->name = $req->name;
            $category->description = $req->description;
            $category->save();

            return response()->json([
                'status' => 'success',
                'message' => 'แก้ไขหมวดหมู่สำเร็จ',
                'data' => $category,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $e->errors(),
            ], 422);
        }
    }
    public function destroy($id)
    {
        try {
            $category = Categories_Model::findOrFail($id);
            $category->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'ลบหมวดหมู่สำเร็จ',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบหมวดหมู่ที่ต้องการลบ',
            ], 404);
        }
    }
}

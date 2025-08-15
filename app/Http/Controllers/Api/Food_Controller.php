<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Food_Model;
use App\Models\Categories_Model;
class Food_Controller extends Controller
{
    public function Store(Request $req)
    {
        $user = $req->user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }
        $req->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ], [
            'name.required' => 'กรุณากรอกชื่อเมนู',
            'name.max' => 'ชื่อเมนูต้องไม่เกิน 255 ตัวอักษร',
            'price.required' => 'กรุณากรอกราคาเมนู',
            'price.numeric' => 'ราคาเมนูต้องเป็นตัวเลข',
            'category_id.required' => 'กรุณาเลือกหมวดหมู่เมนู',
            'category_id.exists' => 'หมวดหมู่ที่เลือกไม่มีอยู่ในระบบ',
        ]);

        if ($req->hasFile('image')) {
            $image = $req->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $image_path = '/images/Food/' . $imageName;
            $image->move(public_path('images/Food'), $imageName);

        } else {
            $image_path = null;
        }

        $data = [
            'id' => Str::uuid()->toString(),

            'name' => $req->name,
            'description' => $req->description,
            'price' => $req->price,
            'category_id' => $req->category_id,
            'image' => $image_path,
        ];

        Food_Model::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'เพิ่มเมนูสำเร็จ',
            'data' => $data
        ]);
    }
    function index()
    {
        $foods = Food_Model::all();
        $categories = Categories_Model::all()->keyBy('id');

        return response()->json([
            'data' => $foods->toArray(),
        ]);
    }
    function update(Request $req, $id)
    {

        $req->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'name.required' => 'กรุณากรอกชื่อเมนู',
            'name.max' => 'ชื่อเมนูต้องไม่เกิน 255 ตัวอักษร',
            'price.required' => 'กรุณากรอกราคาเมนู',
            'price.numeric' => 'ราคาเมนูต้องเป็นตัวเลข',
            'category_id.required' => 'กรุณาเลือกหมวดหมู่เมนู',
            'category_id.exists' => 'หมวดหมู่ที่เลือกไม่มีอยู่ในระบบ',
            'image.image' => 'ไฟล์ที่อัพโหลดต้องเป็นรูปภาพ',
        ]);

        $food = Food_Model::findOrFail($id);

        if ($req->hasFile('image')) {

            if ($food->image) {
                $oldImageName = basename($food->image); // เอาแค่ชื่อไฟล์
                $oldImagePath = public_path('images/Food/' . $oldImageName);

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $image = $req->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $image_path = '/images/Food/' . $imageName;
            $image->move(public_path('images/Food'), $imageName);
        } else {
            $image_path = $food->image;
        }

        $data = [
            'name' => $req->name,
            'description' => $req->description,
            'price' => $req->price,
            'category_id' => $req->category_id,
            'image' => $image_path,
        ];


        Food_Model::where('id', $id)->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'แก้ไขเมนูสำเร็จ',
            'data' => $food->toArray()
        ]);
    }
    function destroy($id)
    {
        $data = Food_Model::findOrFail($id);
        if ($data->image) {
            $path = basename($data->image);
            $imagePath = public_path('images/Food/' . $path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

        }
        Food_Model::destroy($id);
        return response()->json([
            'status' => 'success',
            'message' => 'ลบเมนูสำเร็จ',
        ], 200);
    }
}

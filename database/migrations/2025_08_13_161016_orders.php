<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('table_number', 10);
            $table->string('queue_code', 20);
            $table->integer('queue_number'); // เลขคิว
            $table->enum('status_queue', ['รบออเดอร์','เสิร์ฟแล้ว','เสร็จสิ้น'])->default('รบออเดอร์');
            $table->enum('status_payment', ['ยังไม่จ่าย','จ่ายแล้ว'])->default('ยังไม่จ่าย');
            $table->decimal('total_price', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};

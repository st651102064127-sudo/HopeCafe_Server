<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');      // id ผู้ใช้
            $table->string('session_id')->unique();     // session id
            $table->string('ip_address')->nullable();   // IP ของผู้ใช้
            $table->string('user_agent')->nullable();   // browser info
            $table->boolean('is_online')->default(true);// สถานะ online/offline
            $table->timestamp('last_activity')->nullable(); // เวลา last activity
            $table->timestamps();

            // foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

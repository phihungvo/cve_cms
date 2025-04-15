<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('frequency')->nullable();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
        });

        // Thêm ràng buộc kiểm tra start_time < end_time
        // Lưu ý: MySQL không hỗ trợ CHECK constraint trước phiên bản 8.0.16, nếu dùng MySQL cũ, bạn cần bỏ đoạn này
        Schema::table('schedule_details', function (Blueprint $table) {
            DB::statement('ALTER TABLE schedule_details ADD CONSTRAINT check_schedule_time CHECK (start_time < end_time)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_details');
    }
};

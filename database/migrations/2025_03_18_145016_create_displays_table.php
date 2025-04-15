<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('displays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id')->nullable()->constrained('display_statuses')->onDelete('set null');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('playlist_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('displays');
    }
};

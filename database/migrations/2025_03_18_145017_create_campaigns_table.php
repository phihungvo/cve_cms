<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('campaign', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('performance_id')->nullable()->constrained('performances')->onDelete('set null');
            $table->foreignId('media_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->bigInteger('user_id')->nullable();
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign');
    }
};

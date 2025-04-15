<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audiences', function (Blueprint $table) {
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('location')->nullable();
            $table->text('behavior')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audiences');
    }
};

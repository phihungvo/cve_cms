<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->decimal('total', 15, 2);
            $table->decimal('spent', 15, 2);
            $table->decimal('remaining', 15, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};

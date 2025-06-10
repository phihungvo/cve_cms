<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('performance', function (Blueprint $table) {
            $table->id();
            $table->integer('reach')->default(0);
            $table->integer('impression')->default(0);
            $table->integer('distance')->default(0);
            $table->integer('cpm')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance');
    }
};

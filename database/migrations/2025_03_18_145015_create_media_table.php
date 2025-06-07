<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('media_url');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('campaign_id')->constrained('campaign')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
};

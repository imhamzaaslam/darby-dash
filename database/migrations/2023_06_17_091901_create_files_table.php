<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('fileable_type');
            $table->unsignedInteger('fileable_id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->string('mime_type')->nullable();
            $table->string('path');
            $table->enum('type', ['avatar', 'document'])->nullable();
            $table->unsignedInteger('size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};

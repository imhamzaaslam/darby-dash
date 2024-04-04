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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('platform_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
            $table->string('uid')->unique()->nullable();
        });

        Schema::create('product_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            $table->string('title')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->string('purchase_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_user');
        Schema::dropIfExists('products');
    }
};

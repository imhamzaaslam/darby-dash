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
        Schema::create('settings_meta', function (Blueprint $table) {
            $table->id();

            $table->integer('setting_id')->unsigned()->index();
            $table->integer('user_id')->nullable();
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->string('deliverable_channel')->default('null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_meta');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('platform_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
            $table->string('uid')->unique()->nullable();
            $table->enum('type', ['daily', 'monthly']);
            $table->integer('year');
            $table->enum('month', $this->months);
            $table->integer('day')->nullable();
            $table->string('amount');
            $table->string('vat')->nullable();
            $table->string('total_amount');
            $table->longText('payload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};

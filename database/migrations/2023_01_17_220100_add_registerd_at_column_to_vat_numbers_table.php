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
        Schema::table('vat_numbers', function (Blueprint $table) {
            $table->dateTime('registered_at')->nullable()->after('deleted_at');
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly'])->default('quarterly');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vat_numbers', function (Blueprint $table) {
            $table->dropColumn(['registered_at', 'frequency']);
        });
    }
};

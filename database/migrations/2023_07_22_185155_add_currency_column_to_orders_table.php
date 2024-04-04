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
    public function up(): void
    {
        $currencies = [
            'BGN',
            'CHF',
            'CZK',
            'DKK',
            'EUR',
            'GBP',
            'HRK',
            'HUF',
            'NOK',
            'PLN',
            'RON',
            'RSD',
            'RUB',
            'SEK',
            'TRY',
        ];

        Schema::table('orders', function (Blueprint $table) use ($currencies) {
            $table->enum('currency', $currencies)->after('uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};

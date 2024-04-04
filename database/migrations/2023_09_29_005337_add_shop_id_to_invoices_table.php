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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('shop_id')->nullable()->after('platform_id')->constrained();

            /*
             * These should be unused in the future. For now, we will make it nullable.
             * When the system has fully embraced the shop relation, we'll create a new migration which drops these
             * redundant foreign keys.
             */
            $table->foreignId('platform_id')->nullable()->change();
            $table->foreignId('user_id')->nullable()->change();

        });

        Schema::table('credentials', function (Blueprint $table) {
            $table->foreignId('shop_id')->nullable()->after('platform_id')->constrained();

            /*
             * These should be unused in the future. For now, we will make it nullable.
             * When the system has fully embraced the shop relation, we'll create a new migration which drops these
             * redundant foreign keys.
             */
            $table->foreignId('platform_id')->nullable()->change();
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shop_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shop_id');
        });
    }
};

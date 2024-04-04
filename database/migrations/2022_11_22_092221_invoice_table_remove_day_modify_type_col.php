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
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('day');
            DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('BOL_RETAIL_MEDIA_GROUP', 'ALL_IN_ONE');");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('day')->nullable()->after('month');
        });
        DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('daily', 'monthly');");
    }
};

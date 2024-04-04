<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('BOL_RETAIL_MEDIA_GROUP', 'ALL_IN_ONE', 'ADVERTISING_VIA_BOL');");
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
            DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('BOL_RETAIL_MEDIA_GROUP', 'ALL_IN_ONE');");
        });
    }
};

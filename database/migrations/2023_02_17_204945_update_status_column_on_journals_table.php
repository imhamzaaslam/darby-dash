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
        Schema::table('journals', function (Blueprint $table) {
            \DB::statement(
                "ALTER TABLE `journals`
                MODIFY COLUMN `status`
                ENUM('created', 'pending', 'approved', 'disapproved', 'booked', 'failed', 'manual') NOT NULL DEFAULT 'created';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journals', function (Blueprint $table) {
            \DB::statement(
                "ALTER TABLE `journals`
                MODIFY COLUMN `status`
                ENUM('created','pending','approved', 'disapproved', 'booked', 'manual') NOT NULL DEFAULT 'created';");
        });
    }
};

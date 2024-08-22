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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('budget_amount')->nullable()->after('status');
            $table->string('bucks_share')->nullable()->after('budget_amount');
            $table->enum('bucks_share_type', ['fixed', 'percentage'])->default('fixed')->nullable()->after('bucks_share');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};

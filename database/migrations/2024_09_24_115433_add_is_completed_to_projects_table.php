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
            $table->timestamp('completed_at')->nullable()->after('bucks_share_type');
            $table->text('comments')->nullable()->after('bucks_share_type');
            $table->boolean('is_pm_bucks_awarded')->default(false)->after('bucks_share_type');
            $table->boolean('is_completed')->default(false)->after('bucks_share_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('is_completed');
            $table->dropColumn('is_pm_bucks_awarded');
            $table->dropColumn('comments');
        });
    }
};

<?php

use App\Enums\Platform as PlatformEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->string('client')->nullable();
        });

        if (app()->environment('production')) {
            // migration for production build.
            $bol = DB::table('platforms')
                ->where('name', '=', 'Bol.com')
                ->exists();

            if (!$bol) {
                DB::table('platforms')->insert([
                    'uuid' => Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'name' => 'Bol.com',
                    'client' => 'Bol.com',
                    'state' => 'active',
                ]);
            } else {
                DB::table('platforms')
                    ->where('name', '=', PlatformEnum::BOL_COM->value)
                    ->update(['client' => PlatformEnum::BOL_COM->value]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn('client');
        });
    }
};

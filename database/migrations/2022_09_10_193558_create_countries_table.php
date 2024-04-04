<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $countries = [
        ['name' => 'Austria', 'code' => 'AT', 'is_taxable' => 0],
        ['name' => 'Belgium', 'code' => 'BE', 'is_taxable' => 0],
        ['name' => 'Bulgaria', 'code' => 'BG', 'is_taxable' => 0],
        ['name' => 'Croatia', 'code' => 'HR', 'is_taxable' => 0],
        ['name' => 'Cyprus', 'code' => 'CY', 'is_taxable' => 0],
        ['name' => 'Czech Republic', 'code' => 'CZ', 'is_taxable' => 1],
        ['name' => 'Denmark', 'code' => 'DK', 'is_taxable' => 0],
        ['name' => 'Estonia', 'code' => 'EE', 'is_taxable' => 0],
        ['name' => 'Europe', 'code' => 'EU', 'is_taxable' => 0],
        ['name' => 'Finland', 'code' => 'FI', 'is_taxable' => 0],
        ['name' => 'France', 'code' => 'FR', 'is_taxable' => 1],
        ['name' => 'Germany', 'code' => 'DE', 'is_taxable' => 1],
        ['name' => 'Greece', 'code' => 'GR', 'is_taxable' => 0],
        ['name' => 'Hungary', 'code' => 'HU', 'is_taxable' => 0],
        ['name' => 'Ireland', 'code' => 'IE', 'is_taxable' => 0],
        ['name' => 'Italy', 'code' => 'IT', 'is_taxable' => 1],
        ['name' => 'Latvia', 'code' => 'LV', 'is_taxable' => 0],
        ['name' => 'Lithuania', 'code' => 'LT', 'is_taxable' => 0],
        ['name' => 'Luxembourg', 'code' => 'LU', 'is_taxable' => 0],
        ['name' => 'Malta', 'code' => 'MT', 'is_taxable' => 0],
        ['name' => 'Moldova, Republic of', 'code' => 'MD', 'is_taxable' => 0],
        ['name' => 'Netherlands', 'code' => 'NL', 'is_taxable' => 1],
        ['name' => 'Norway', 'code' => 'NO', 'is_taxable' => 0],
        ['name' => 'Poland', 'code' => 'PL', 'is_taxable' => 1],
        ['name' => 'Portugal', 'code' => 'PT', 'is_taxable' => 0],
        ['name' => 'Romania', 'code' => 'RO', 'is_taxable' => 0],
        ['name' => 'Russian Federation', 'code' => 'RU', 'is_taxable' => 0],
        ['name' => 'Slovakia', 'code' => 'SK', 'is_taxable' => 0],
        ['name' => 'Slovenia', 'code' => 'SI', 'is_taxable' => 0],
        ['name' => 'Spain', 'code' => 'ES', 'is_taxable' => 1],
        ['name' => 'Sweden', 'code' => 'SE', 'is_taxable' => 0],
        ['name' => 'Switzerland', 'code' => 'CH', 'is_taxable' => 0],
        ['name' => 'Turkey', 'code' => 'TR', 'is_taxable' => 0],
        ['name' => 'United Kingdom', 'code' => 'GB', 'is_taxable' => 0],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->boolean('is_taxable')->default(0);
        });

        DB::table('countries')->insertTs($this->countries);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};

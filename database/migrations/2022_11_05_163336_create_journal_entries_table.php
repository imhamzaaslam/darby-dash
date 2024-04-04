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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
            $table->string('contact_name')->nullable();
            $table->string('contact_code')->nullable();
            $table->string('dossier_name')->nullable();
            $table->string('dossier_code')->nullable();
            $table->string('entry_date')->nullable();
            $table->string('general_ledger_account');
            $table->integer('amount');
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entries');
    }
};

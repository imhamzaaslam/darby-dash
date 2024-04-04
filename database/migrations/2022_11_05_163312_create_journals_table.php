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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('invoice_id')->constrained();
            $table->foreignId('platform_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
            $table->string('administration_key')->nullable();
            $table->string('administration_coc_number')->nullable();
            $table->string('document_subject')->nullable();
            $table->enum('journal_type', ['GeneralJournal', 'EndOfYearCorrection', 'FiscalCorrection'])->nullable();
            $table->string('project_key')->nullable();
            $table->string('project_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journals');
    }
};

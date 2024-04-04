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
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('journal_id')->constrained();
            $table->unique(['journal_id', 'country_id', 'general_ledger_account'], 'general_ledger_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('journal_entries');

            if(!array_key_exists('journal_entries_journal_id_foreign', $indexesFound)) {
                $table->index('journal_id', 'journal_entries_journal_id_foreign');
            }

            $table->dropUnique('general_ledger_unique');
            $table->dropForeign('journal_entries_country_id_foreign');
            $table->dropColumn(['country_id']);
        });
    }
};

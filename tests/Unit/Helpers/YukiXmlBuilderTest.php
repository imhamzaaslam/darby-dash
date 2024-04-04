<?php

namespace Tests\Unit\Helpers;

use App\Helpers\YukiXmlBuilder;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class YukiXmlBuilderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_journal_model_converted_xml()
    {
        $user = User::factory()->create();
        UserInfo::factory()->recycle($user)->create(['coc_number' => "123456789"]);

        $shop = Shop::factory()->recycle($user)->create();

        $invoice = Invoice::factory()->recycle($shop)->create(['month' => 4]);

        $journal = Journal::factory()->recycle($shop)->recycle($invoice)->create([
            'document_subject' => '2022-12-16 | 63276214',
            'administration_coc_number' => '12345672',
            'journal_type' => 'GeneralJournal',
            'project_key' => '',
            'project_code' => '',
        ]);

        JournalEntry::factory()->recycle($journal)->create([
            'country_id' => null,
            'contact_name' => 'Apple Sales International',
            'contact_code' => '9921',
            'entry_date' => '2012-12-31',
            'general_ledger_account' => '18800',
            'amount' => make_integer(22.22),
        ]);

        JournalEntry::factory()->recycle($journal)->create([
            'country_id' => null,
            'contact_name' => 'Apple Sales International',
            'contact_code' => '9921',
            'entry_date' => '2012-12-31',
            'general_ledger_account' => '18800',
            'amount' => make_integer(-22.22),
        ]);

        $expected = $this->sampleXml();
        $actual = YukiXmlBuilder::convert($journal);

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function sampleXml(): string
    {
        return '<Journal xmlns="urn:xmlns:http://www.theyukicompany.com:journal"

                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

        <AdministrationCoCNumber>123456789</AdministrationCoCNumber>

        <DocumentSubject>2022-12-16 | 63276214</DocumentSubject>

        <JournalType>GeneralJournal</JournalType>

        <ProjectID></ProjectID>

        <ProjectCode></ProjectCode>

        <JournalEntry>

                <ContactName>Apple Sales International</ContactName>

                <ContactCode>9921</ContactCode>

                <EntryDate>2012-12-31</EntryDate>

                <GLAccount>18800</GLAccount>

                <Amount>22.22</Amount>

                <Description>To receive VAT</Description>

        </JournalEntry>

        <JournalEntry>

                <ContactName>Apple Sales International</ContactName>

                <ContactCode>9921</ContactCode>

                <EntryDate>2012-12-31</EntryDate>

                <GLAccount>18800</GLAccount>

                <Amount>-22.22</Amount>

                <Description>To receive VAT</Description>

        </JournalEntry></Journal>';
    }
}

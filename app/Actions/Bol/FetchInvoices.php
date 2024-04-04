<?php

namespace App\Actions\Bol;

use App\Contracts\InvoiceRepositoryInterface;
use App\Enums\InvoiceType;
use App\Enums\Platform as PlatformEnum;
use App\Models\Shop;
use App\Services\Bol\BolClient;
use Exception;
use Illuminate\Support\Facades\Log;

class FetchInvoices
{
    protected BolClient $client;

    protected InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(protected Shop $shop)
    {
        $platformName = PlatformEnum::BOL_COM->value;

        if ($shop->platform->client !== $platformName) {
            throw new \InvalidArgumentException("Shop {$shop->uuid} does not belong to {$platformName}");
        }

        $this->client = app(BolClient::class);
        $this->invoiceRepository = app(InvoiceRepositoryInterface::class);
    }

    /**
     * @throws Exception
     */
    public function fetch(\Illuminate\Support\Carbon $date = null): void
    {
        Log::info(__CLASS__ . '::' . __LINE__ . ' starting...');

        try {
            // we always want invoices from a monthly period, usually of the last month.
            // Note that we use subMonthNoOverflow to correctly get the date of February, which doesn't have 30/31 days.
            $invoices = $this->client->getInvoices(
                $this->shop,
                $date?->copy()?->firstOfMonth() ?? null,
                $date?->copy()?->lastOfMonth() ?? null
            );

            $successLogs = [];
            $errorLogs = [];
            foreach ($invoices['invoiceListItems'] as $invoice) {
                try {
                    $bolInvoiceId = $invoice['invoiceId'];

                    if (null === $this->invoiceRepository->getFirstBy('uid', $bolInvoiceId)) {
                        $data = $this->client->getInvoice($this->shop, $bolInvoiceId);

                        $invoiceType = InvoiceType::tryFrom($invoice['invoiceType']);

                        $this->invoiceRepository->create($this->shop, $data, $invoiceType);
                    }
                    $successLogs[] = __CLASS__ . '::' . __LINE__ . " Invoice {$bolInvoiceId} created";
                } catch (Exception $e) {
                    $errorLogs[] = __CLASS__ . '::' . __LINE__ . " Exception while fetching invoice {$bolInvoiceId}: {$e->getMessage()}";
                    continue;
                }
            }
            if (!empty($successLogs)) {
                Log::info(implode("\n", $successLogs));
            }
            if (!empty($errorLogs)) {
                Log::error(implode("\n", $errorLogs));
            }
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __LINE__ . " Exception while fetching invoices: {$e->getMessage()}");
            throw $e;
        }
        Log::info(__CLASS__ . '::' . __LINE__ . ' ran successfully!');
    }
}

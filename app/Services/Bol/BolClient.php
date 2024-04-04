<?php

namespace App\Services\Bol;

use App\Enums\InvoiceLineType;
use App\Enums\Platform;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\RateLimitException;
use App\Exceptions\ServerException;
use App\Exceptions\UnauthorizedTokenException;
use App\Models\AccessToken;
use App\Models\Credential;
use App\Models\Shop;
use App\Models\User;
use App\Services\BaseClient;
use Illuminate\Support\Carbon;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\File;

class BolClient extends BaseClient
{
    private array $fakeOrderIds = [
        '1042823870',
        '1043965710',
        '1042831430',
        'A4K8290LP0',
        '1043946570',
        'B3K8290LP0',
    ];

    private string $fakeInvoiceId = '4500022543921';

    public function getToken(): ?AccessToken
    {
        return $this->accessToken;
    }

    public function isAuthenticated(): bool
    {
        return $this->getToken() && (now() < $this->getToken()->expires_at);
    }

    public function getEndpoint(): string
    {
        return config('services.bol_com.endpoint');
    }

    protected function getContentType(): string
    {
        return config('services.bol_com.content_type_json');
    }

    /**
     * @throws ServerException
     * @throws RequestException
     * @throws RateLimitException
     * @throws HttpClientException
     * @throws UnauthorizedTokenException
     * @throws InvalidTokenException
     */
    public function getInvoices(Shop $shop, Carbon $periodStartDate = null, Carbon $periodEndDate = null): ?array
    {
        $this->authenticate($shop);

        $url = 'invoices';

        $options = [
            'query' => [
                'period-start-date' => $periodStartDate?->format('Y-m-d') ?? null,
                'period-end-date' => $periodEndDate?->format('Y-m-d') ?? null,
            ],
        ];

        return $this->get($url, $options);
    }

    public function getInvoice(Shop $shop, string $orderId): ?array
    {
        $this->authenticate($shop);

        $url = "invoices/{$orderId}";

        return $this->get($url, []);
    }

    public function getInvoiceSpecification(Shop $shop, string $invoiceId): ?array
    {
        $this->authenticate($shop);

        if (!app()->environment(['production', 'development'])) {
            $invoiceId = $this->fakeInvoiceId;
        }

        $url = "invoices/{$invoiceId}/specification";

        return $this->get($url, []);
    }

    /**
     * @throws ServerException
     * @throws RequestException
     * @throws RateLimitException
     * @throws InvalidTokenException
     * @throws HttpClientException
     * @throws UnauthorizedTokenException
     */
    public function getOrders(Shop $shop, string $fulfilledBy = 'FBB', string $type = 'all'): ?array
    {
        $this->authenticate($shop);

        $url = 'orders';

        $options = [
            'query' => [
                'status' => strtoupper($type),
                'fulfilment-method' => strtoupper($fulfilledBy)
            ]
        ];

        return $this->get($url, $options);
    }

    /**
     * @throws ServerException
     * @throws RequestException
     * @throws RateLimitException
     * @throws HttpClientException
     * @throws UnauthorizedTokenException|InvalidTokenException
     */
    public function getOrder(Shop $shop, string $orderId): ?array
    {
        $this->authenticate($shop);

        if (!app()->environment(['production', 'development'])) {
            $randomKey = array_rand($this->fakeOrderIds);
            $orderId = $this->fakeOrderIds[$randomKey];
        }

        $url = "orders/{$orderId}";

        return $this->get($url, []);
    }

    public function getSoldProducts(Shop $shop): ?array
    {
        $this->authenticate($shop);

        $orders = $this->getOrders($credential);

        $soldProducts = [];

        foreach ($orders['orders'] as $order) {
            foreach ($order['orderItems'] as $orderItem) {
                $soldProducts[] = $orderItem;
            }
        }

        return $soldProducts;
    }

    public function getReturns(Shop $shop): ?array
    {
        $this->authenticate($shop);

        $url = 'returns';

        $options = [
            'query' => [
                'handled' => true,
            ],
        ];

        $handledReturns = $this->get($url, $options, 'returns');

        $options['query']['handled'] = false;

        $unhandledReturns = $this->get($url, $options, 'unhandled-returns');

        $returns = [];

        foreach ($handledReturns['returns'] as $return) {
            $returns[] = $return;
        }

        foreach ($unhandledReturns['returns'] as $return) {
            $returns[] = $return;
        }

        return $returns;
    }

    public function getInventory(Shop $shop): ?array
    {
        $this->authenticate($shop);

        $url = 'inventory';

        return $this->get($url, []);
    }

    /**
     * @throws InvalidTokenException
     */
    protected function authenticate(Shop $shop): void
    {
        $this->setAccessToken($shop);
    }
}

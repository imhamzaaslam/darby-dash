<?php

namespace App\Services\Bol;

use App\Enums\Platform as PlatformEnum;
use App\Models\Credential;
use App\Models\Platform;
use App\Services\AuthClientInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Exception;
use App\Exceptions\InvalidTokenException;

readonly class AuthClient implements AuthClientInterface
{
    protected Platform $platform;

    protected string $clientId;

    protected string $clientSecret;

    protected string|array $token;

    public function __construct(protected Credential $credential)
    {
        $this->platform = $this->credential->shop->platform;
        $this->clientId = $this->credential->client_id;
        $this->clientSecret = $this->credential->client_secret;
    }

    /**
     * @throws RequestException
     * @throws InvalidTokenException
     */
    public function authenticate(): void
    {
        $credentials = base64_encode(sprintf('%s:%s', $this->clientId, $this->clientSecret));

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Basic {$credentials}",
        ])->asForm()->post($this->getTokenEndpoint(), [
            'grant_type' => 'client_credentials'
        ])->throw();

        $token = $response->json();
        $this->validateToken($token);

        $token['expires_at'] = time() + $token['expires_in'] ?? 0;
        $this->setToken($token);
    }

    public function setToken(array $token): void
    {
        $this->token = $token;
    }

    public function getToken(): array
    {
        return $this->token;
    }

    public function getTokenEndpoint(): ?string
    {
        if ($this->platform->client !== PlatformEnum::BOL_COM->value) {
            return null;
        }

        return config('services.bol_com.access_token_endpoint');
    }

    /**
     * @throws InvalidTokenException
     */
    public function validateToken(array $token): void
    {
        if (empty($token['access_token'])) {
            throw new InvalidTokenException('Missing access_token');
        }

        if (empty($token['expires_in'])) {
            throw new InvalidTokenException('Missing expires_in');
        }

        if (strtolower($token['token_type']) !== 'bearer') {
            throw new InvalidTokenException(
                sprintf('Unexpected token_type \'%s\', expected \'Bearer\'', $token['token_type'])
            );
        }

        if (strtolower($token['scope']) !== 'retailer') {
            throw new InvalidTokenException(
                sprintf('Unexpected scope \'%s\', expected \'RETAILER\'', $token['scope'])
            );
        }
    }
}

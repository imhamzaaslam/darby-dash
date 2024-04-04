<?php

namespace App\Services;

use App\Exceptions\InvalidTokenException;
use App\Models\Credential;
use App\Models\Shop;
use App\Services\Bol\AuthClient;
use Illuminate\Support\Facades\Http;
use App\Exceptions\UnauthorizedTokenException;
use Illuminate\Http\Client\RequestException;
use App\Exceptions\RateLimitException;
use App\Exceptions\ServerException;
use App\Models\AccessToken;
use Illuminate\Http\Client\HttpClientException;

abstract class BaseClient
{
    protected AccessToken $accessToken;

    /**
     * @throws RequestException
     * @throws InvalidTokenException
     */
    protected function requestAccessToken(Credential $credential): AccessToken
    {
        $authClient = new AuthClient(
            credential: $credential
        );

        $authClient->authenticate();
        $token = $authClient->getToken();

        return $credential->accessToken()->create([
            'access_token' => $token['access_token'],
            'expires_at' => $token['expires_at'],
        ]);
    }

    protected function getAccessToken(Credential $credential): ?AccessToken
    {
        try {
            $user = $credential->shop->user;
            // We perform an Eloquent query here, because we always want the direct DB result, instead
            // of retrieving the access token using the Credential relation, which may not represent
            // actual the data at the time of requesting.
            $accessToken = AccessToken::whereCredentialId($credential->id)->latest()->first();

            if (null === $accessToken) {
                activity()
                    ->performedOn($user)
                    ->log("No access token found, requesting new one for user: {$user->uuid}");

                return $this->requestAccessToken($credential);
            }

            if (now() > $accessToken->expires_at) {
                activity()
                    ->performedOn($user)
                    ->log("Access token is expired, requesting new one for user: {$user->uuid}");

                $accessToken->delete();
                return $this->requestAccessToken($credential);
            }

            return $accessToken;
        } catch (\Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * @throws InvalidTokenException
     */
    protected function setAccessToken(Shop $shop): void
    {
        $credential = $shop->credential;

        if (!$credential) {
            throw new \UnexpectedValueException('Credential expected, ' . get_class($credential) . ' given.');
        }

        $accessToken = $this->getAccessToken($shop->credential);

        if (null === $accessToken) {
            throw new InvalidTokenException('Expected access token to be of type \'AccessToken\', null provided.');
        }

        if (empty($this->accessToken)) {
            $this->accessToken = $accessToken;
            return;
        }

        if ($this->accessToken->access_token !== $accessToken->access_token) {
            $this->accessToken = $accessToken;
        }

        // If we get here, that means that we currently have a valid access token that is set.
        // We can proceed with the request.
    }

    /**
     * @throws ServerException
     * @throws RequestException
     * @throws RateLimitException
     * @throws HttpClientException
     * @throws UnauthorizedTokenException
     */
    protected function get(string $url, array $options): ?array
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * @throws ServerException
     * @throws RequestException
     * @throws RateLimitException
     * @throws HttpClientException
     * @throws UnauthorizedTokenException
     */
    protected function post(string $url, array $options)
    {
        return $this->request('POST', $url, $options);
    }

    /**
     * @throws ServerException
     * @throws RateLimitException
     * @throws RequestException
     * @throws HttpClientException
     * @throws UnauthorizedTokenException
     */
    private function request(string $method, string $url, array $options)
    {
        if (!$this->isAuthenticated()) {
            throw new UnauthorizedTokenException('No or expired token, please authenticate first');
        }

        $url = $this->getEndpoint() . $url;

        $httpOptions = [];

        // pass through query parameters without null values
        if (isset($options['query'])) {
            $httpOptions = array_filter($options['query'], function ($value) {
                return $value !== null;
            });
        }

        return $this->rawRequest($method, $url, $httpOptions);
    }

    /**
     * @throws ServerException
     * @throws RateLimitException
     * @throws RequestException
     * @throws HttpClientException
     * @throws UnauthorizedTokenException
     */
    private function rawRequest(string $method, string $url, array $options = [])
    {
        $http = Http::withToken($this->getToken()->access_token)->accept($this->getContentType());

        try {
            switch ($method) {
                case 'GET':

                    return $http->get($url, $options)->throw()->json();

                case 'POST':

                    return $http->asForm()->post($url, $options)->throw()->json();
            }

        } catch (RequestException $e) {
            $response = $e->response;
            $status = $response->status();
            $data = $response->json();
            $reason = $data['detail'] ?? $response->reason();
            $headers = $response->headers();

//            Log::error(sprintf('%s.php@%s: %s', __CLASS__, __LINE__, $reason));

            if ($response->unauthorized()) {
                throw new UnauthorizedTokenException($reason, $status, null, $data);
            }

            if ($response->serverError()) {
                throw new ServerException($reason, $status, null, $data);
            }

            if ($response->clientError() && !$response->unauthorized() && $status !== 429) {
                throw new HttpClientException($reason, $status);
            }

            if ($status === 429) {
                throw new RateLimitException($reason, $status, null, $headers);
            }

            throw $e;
        }
    }

    abstract public function getToken(): ?AccessToken;

    abstract public function getEndpoint(): string;

    abstract public function isAuthenticated(): bool;

    abstract protected function authenticate(Shop $shop): void;

    abstract protected function getContentType(): string;
}

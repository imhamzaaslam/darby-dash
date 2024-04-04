<?php

namespace App\Services;

interface AuthClientInterface
{
    /**
     * @return void
     */
    public function authenticate(): void;

    /**
     * @return string|array
     */
    public function getToken(): string|array;

    /**
     * @return string|null
     */
    public function getTokenEndpoint(): ?string;

    /**
     * @param array $token
     *
     * @return void
     */
    public function validateToken(array $token): void;
}

<?php

declare(strict_types=1);

namespace Altirs;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    private const DEFAULT_BASE_URL = 'https://altirs.ai/api';
    private const DEFAULT_TIMEOUT = 30;

    private HttpClient $http;

    public function __construct(
        private readonly string $apiKey,
        string $baseUrl = self::DEFAULT_BASE_URL,
        int $timeout = self::DEFAULT_TIMEOUT,
    ) {
        $this->http = new HttpClient([
            'base_uri' => rtrim($baseUrl, '/'),
            'timeout' => $timeout,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
                'X-SDK-Version' => 'php/' . self::version(),
            ],
        ]);
    }

    /**
     * Resolved from the installed package metadata so composer.json and the
     * git tag stay the only places the version is declared.
     */
    private static function version(): string
    {
        if (class_exists(\Composer\InstalledVersions::class)) {
            $v = \Composer\InstalledVersions::getPrettyVersion('altirs/sdk');
            if ($v !== null) {
                return $v;
            }
        }

        return 'dev';
    }

    public function check(CheckPayload $payload): CheckResponse
    {
        $data = $this->request('/v1/guardrail/check', $payload->toArray());
        return CheckResponse::fromArray($data);
    }

    /**
     * @param CheckPayload[] $items
     */
    public function batch(array $items): BatchResponse
    {
        $mapped = array_map(fn(CheckPayload $p) => $p->toArray(), $items);
        $data = $this->request('/v1/guardrail/batch', ['items' => $mapped]);
        return BatchResponse::fromArray($data);
    }

    public function isSafe(string $input): bool
    {
        $result = $this->check(new CheckPayload(input: $input));
        return $result->safe;
    }

    public function maskPii(string $text): string
    {
        $result = $this->check(new CheckPayload(
            input: $text,
            checks: ['pii'],
            options: ['pii' => ['mask' => true]],
        ));
        return $result->maskedInput ?? $text;
    }

    private function request(string $path, array $body): array
    {
        try {
            $response = $this->http->post($path, [
                'json' => $body,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
            $data = [];

            if ($e->hasResponse()) {
                $data = json_decode($e->getResponse()->getBody()->getContents(), true) ?? [];
            }

            throw new AltirsException(
                $data['error'] ?? $e->getMessage(),
                $status,
                $data['requestId'] ?? 'unknown',
            );
        } catch (\Throwable $e) {
            throw new AltirsException($e->getMessage(), 0, 'unknown');
        }
    }
}

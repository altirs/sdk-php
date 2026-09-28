<?php

declare(strict_types=1);

namespace Altirs;

class BatchResponse
{
    public function __construct(
        /** @var BatchResultItem[] */
        public readonly array $results,
        public readonly int $total,
        public readonly string $requestId,
        public readonly int $latencyMs,
    ) {}

    public static function fromArray(array $data): self
    {
        $results = array_map(
            fn(array $r) => new BatchResultItem(
                index: $r['index'] ?? 0,
                safe: $r['safe'] ?? false,
                blocked: $r['blocked'] ?? false,
                score: (float) ($r['score'] ?? 0.0),
                violations: $r['violations'] ?? [],
                maskedInput: $r['maskedInput'] ?? null,
                maskedResponse: $r['maskedResponse'] ?? null,
                recommendation: $r['recommendation'] ?? null,
                latencyMs: (int) ($r['latencyMs'] ?? 0),
            ),
            $data['results'] ?? []
        );

        return new self(
            results: $results,
            total: (int) ($data['total'] ?? 0),
            requestId: $data['requestId'] ?? '',
            latencyMs: (int) ($data['latencyMs'] ?? 0),
        );
    }
}

class BatchResultItem
{
    public function __construct(
        public readonly int $index,
        public readonly bool $safe,
        public readonly bool $blocked,
        public readonly float $score,
        public readonly array $violations,
        public readonly ?string $maskedInput,
        public readonly ?string $maskedResponse,
        public readonly ?string $recommendation,
        public readonly int $latencyMs,
    ) {}
}

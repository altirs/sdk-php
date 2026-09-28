<?php

declare(strict_types=1);

namespace Altirs;

class CheckResponse
{
    public function __construct(
        public readonly bool $safe,
        public readonly bool $blocked,
        public readonly float $score,
        /** @var array<array{type: string, category: string, severity: string, description: string}> */
        public readonly array $violations,
        public readonly ?string $maskedInput,
        public readonly ?string $maskedResponse,
        public readonly ?string $recommendation,
        public readonly string $requestId,
        public readonly int $latencyMs,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            safe: $data['safe'] ?? false,
            blocked: $data['blocked'] ?? false,
            score: (float) ($data['score'] ?? 0.0),
            violations: $data['violations'] ?? [],
            maskedInput: $data['maskedInput'] ?? null,
            maskedResponse: $data['maskedResponse'] ?? null,
            recommendation: $data['recommendation'] ?? null,
            requestId: $data['requestId'] ?? '',
            latencyMs: (int) ($data['latencyMs'] ?? 0),
        );
    }
}

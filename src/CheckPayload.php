<?php

declare(strict_types=1);

namespace Altirs;

class CheckPayload
{
    public function __construct(
        public readonly ?string $input = null,
        public readonly ?string $response = null,
        /** @var string[]|null */
        public readonly ?array $checks = null,
        public readonly ?array $options = null,
    ) {}

    public function toArray(): array
    {
        $data = [];
        if ($this->input !== null) $data['input'] = $this->input;
        if ($this->response !== null) $data['response'] = $this->response;
        if ($this->checks !== null) $data['checks'] = $this->checks;
        if ($this->options !== null) $data['options'] = $this->options;
        return $data;
    }
}

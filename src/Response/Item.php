<?php

declare(strict_types=1);

namespace App\Response;

use App\Transformer\TransformerInterface;

final class Item
{
    public function __construct(
        private mixed $data,
        private ?TransformerInterface $transformer = null,
        private array $parseIncludes = []
    ) {
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getTransformer(): ?TransformerInterface
    {
        return $this->transformer;
    }

    public function getParseIncludes(): array
    {
        return $this->parseIncludes;
    }

    public function shouldParseIncludes(): bool
    {
        return !empty($this->parseIncludes);
    }
}

<?php

namespace App\Response;

use App\Transformer\TransformerInterface;

final class Collection
{
    public function __construct(
        private array $data,
        private ?TransformerInterface $transformer = null,
        private array $parseIncludes = []
    ) {
        foreach ($this->data as &$item) {
            if ($item instanceof Item) {
                continue;
            }

            $item = new Item($item, $this->transformer, $this->parseIncludes);
        }
    }

    public function getData(): array
    {
        return $this->data;
    }
}

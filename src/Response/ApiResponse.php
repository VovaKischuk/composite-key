<?php

declare(strict_types=1);

namespace App\Response;

use App\Transformer\TransformerInterface;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ApiResponse extends JsonResponse
{
    private function __construct($data = null, int $status = self::HTTP_OK, array $headers = [], bool $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
    }

    public static function fromPayload(array $payload, int $status): self
    {
        return new self($payload, $status);
    }

    public static function empty(int $status): self
    {
        return new self(null, $status);
    }

    /**
     * @throws AssertionFailedException
     */
    public static function created(Item $resource): self
    {
        return new self(self::transform($resource), self::HTTP_CREATED);
    }

    /**
     * @throws AssertionFailedException
     */
    public static function item(Item $resource, int $status = self::HTTP_OK): self
    {
        return new self(self::transform($resource), $status);
    }

    /**
     * @throws AssertionFailedException
     */
    public static function collection(Collection $collection, int $status = self::HTTP_OK): self
    {
        $resources = \array_map(fn ($data) => self::transform($data), $collection->getData());

        return new self($resources, $status);
    }

    public static function file(UriInterface $uri, string $fileName): self
    {
        $response = new self(file_get_contents((string) $uri), json: true);

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * @throws AssertionFailedException
     */
    private static function transform(Item $resource): array
    {
        $transformer = $resource->getTransformer();
        if ($transformer === null) {
            return $resource->getData();
        }

        $data = $transformer->transform($resource->getData());
        if (!$resource->shouldParseIncludes()) {
            return $data;
        }

        $includedData = self::transformIncluded($transformer, $resource);

        return $data + $includedData;
    }

    /**
     * @throws AssertionFailedException
     */
    private static function transformIncluded(TransformerInterface $transformer, Item $resource): array
    {
        $includedData = [];
        $includes = \array_intersect($transformer->getIncludes(), $resource->getParseIncludes());
        foreach ($includes as $includeName) {
            Assertion::string($includeName);
            $method = 'include'.\ucfirst($includeName);
            Assertion::methodExists($method, $transformer);
            $data = $transformer->{$method}($resource->getData());

            if ($data instanceof Item) {
                $includedData[$includeName] = self::transform($data);
            }

            if ($data instanceof Collection) {
                if (empty($data->getData())) {
                    $includedData[$includeName] = [];

                    continue;
                }

                foreach ($data->getData() as $item) {
                    $includedData[$includeName][] = self::transform($item);
                }
            }
        }

        return $includedData;
    }
}

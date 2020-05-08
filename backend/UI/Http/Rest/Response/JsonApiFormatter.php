<?php

declare(strict_types=1);

namespace PlayOrPay\UI\Http\Rest\Response;

use PlayOrPay\Application\Query\Collection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

final class JsonApiFormatter
{
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function one(?object $entity): string
    {
        return $this->serializer->serialize(['data' => $entity], JsonEncoder::FORMAT);
    }

    public function collection(Collection $collection): string
    {
        return $this->serializer->serialize([
            'meta' => [
                'size'  => $collection->limit,
                'page'  => $collection->page,
                'pages' => $collection->pages,
                'total' => $collection->total,
                'refs'  => $collection->refs,
            ],
            'data' => $collection->data,
        ], JsonEncoder::FORMAT);
    }
}

<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Repository;

use rekrutacja4\RestClient\Model\Producer;

class ProducerRepository extends AbstractRepository
{
    private const PATH = '/shop_api/v1/producers';

    /**
     * @return Producer[]
     */
    public function getAll(): array
    {
        $data = $this->request('GET', self::PATH);

        // możliwe kształty: ['producers' => [...]] lub lista [...], lub pojedynczy obiekt
        if (isset($data['producers']) && is_array($data['producers'])) {
            $items = $data['producers'];
        } elseif (is_array($data) && self::isList($data)) {
            $items = $data;
        } elseif (is_array($data) && isset($data['id'])) {
            // pojedynczy obiekt zwrócony bez otaczającej listy
            $items = [$data];
        } else {
            $items = [];
        }

        $result = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $result[] = Producer::fromArray($item);
        }

        return $result;
    }

    private static function isList(array $arr): bool
    {
        return array_values($arr) === $arr;
    }

    public function createOne(Producer $producer): Producer
    {
        // API expects the producer payload wrapped under "producer"
        $payload = ['producer' => $producer->toArray()];
        $data = $this->request('POST', self::PATH, $payload);

        // response may be { producer: {...} } inside data or direct producer object
        $producerData = $data['producer'] ?? $data;
        return Producer::fromArray((array)$producerData);
    }
}

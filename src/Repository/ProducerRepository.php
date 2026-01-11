<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Repository;

use rekrutacja4\RestClient\Model\Producer;

class ProducerRepository extends AbstractRepository
{
    private const PATH = '/producers';

    /**
     * @return Producer[]
     */
    public function getAll(): array
    {
        $data = $this->request('GET', self::PATH);
        $items = $data['items'] ?? $data;
        $result = [];
        foreach ($items as $item) {
            $result[] = Producer::fromArray($item);
        }
        return $result;
    }

    public function createOne(Producer $producer): Producer
    {
        $payload = $producer->toArray();
        $data = $this->request('POST', self::PATH, $payload);
        return Producer::fromArray($data);
    }
}

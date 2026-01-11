<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Repository;

use rekrutacja4\RestClient\Model\Producer;

class ProducerRepository extends AbstractRepository
{
    private const PATH = '/shop_api/v1/producers';

    public function createOne(Producer $producer): Producer
    {
        // API expects the producer payload wrapped under "producer"
        $payload = ['producer' => $producer->toArray()];
        $data = $this->request('POST', self::PATH, $payload);

        // response may be { producer: {...} } inside data or direct producer object
        /**
         * @var array{
         *    name: string,
         *    id: int,
         *    site_url: string,
         *    logo_filename: string,
         *    ordering: int,
         *    source_id: string,
         *  } $producerData
         */
        $producerData = $data['producer'] ?? $data;
        return Producer::fromArray($producerData);
    }
}

<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Query;

use rekrutacja4\RestClient\Exception\ApiException;
use rekrutacja4\RestClient\Http\ClientInterface;
use rekrutacja4\RestClient\Model\Producer;

final  class ProducerQuery extends AbstractQuery
{
    private const string PATH = '/shop_api/v1/producers';

    public function __construct(ClientInterface $http, string $baseUri)
    {
        parent::__construct($http, $baseUri);
    }

    /**
     * @return Producer[]
     *
     * @throws ApiException
     */
    public function getAll(): array
    {
        $data = $this->request('GET', self::PATH);

        if (!isset($data['producers']) || !is_array($data['producers'])) {
            throw new ApiException('Invalid response from API');
        }

        /**
         * @var array<array{
         *     name: string,
         *     id: int,
         *     site_url: string,
         *     logo_filename: string,
         *     ordering: int,
         *     source_id: string,
         * }> $items
         */
        $items = $data['producers'];

        $result = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $result[] = Producer::fromArray($item);
        }

        return $result;
    }
}

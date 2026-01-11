<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Query;

use rekrutacja4\RestClient\Exception\ApiException;
use rekrutacja4\RestClient\View\ProducerView;

final  class ProducerQuery extends AbstractQuery
{
    private const string PATH = '/shop_api/v1/producers';

    /**
     * @return ProducerView[]
     *
     * @throws ApiException
     */
    public function getAll(): array
    {
        $data = $this->get(self::PATH);

        /**
         * @var array<array{
         *     name: string,
         *     id: int,
         *     site_url: string,
         *     logo_filename: string,
         *     ordering: int,
         *     source_id: string,
         * }> $producers
         */
        $producers = $data['producers'] ?? $data;
        if (!is_array($producers)) {
            throw new ApiException('Invalid response from API');
        }

        $result = [];
        foreach ($producers as $producer) {
            if (!is_array($producer)) {
                continue;
            }
            $result[] = ProducerView::fromArray($producer);
        }

        return $result;
    }
}

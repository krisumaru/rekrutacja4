<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\View;

final readonly class ProducerView
{
    public function __construct(
        public string $name,
        public int $id,
        public ?string $siteUrl,
        public string $logoFilename,
        public int $ordering,
        public ?string $sourceId,
    ) {
    }

    /**
     * @param array{
     *   name: string,
     *   id: int,
     *   site_url: string|null,
     *   logo_filename: string,
     *   ordering: string|int,
     *   source_id: string,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['id'],
            $data['site_url'],
            $data['logo_filename'],
            (int) $data['ordering'],
            $data['source_id'],
        );
    }

    /**
     * @return array{
     *   name: string,
     *   id: int,
     *   site_url: string|null,
     *   logo_filename: string,
     *   ordering: int,
     *   source_id: string|null,
     * }
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'site_url' => $this->siteUrl,
            'logo_filename' => $this->logoFilename,
            'ordering' => $this->ordering,
            'source_id' => $this->sourceId,
        ];
    }
}

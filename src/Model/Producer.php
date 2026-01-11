<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Model;

class Producer
{
    public ?int $id;
    public string $name;
    public ?string $siteUrl;
    public ?string $logoFilename;
    public ?int $ordering;
    public ?string $sourceId;

    public function __construct(
        string $name,
        ?int $id = null,
        ?string $siteUrl = null,
        ?string $logoFilename = null,
        ?int $ordering = null,
        ?string $sourceId = null
    ) {
        $this->name = $name;
        $this->id = $id;
        $this->siteUrl = $siteUrl;
        $this->logoFilename = $logoFilename;
        $this->ordering = $ordering;
        $this->sourceId = $sourceId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? $data['title'] ?? '',
            isset($data['id']) ? (int)$data['id'] : null,
            $data['site_url'] ?? null,
            $data['logo_filename'] ?? null,
            isset($data['ordering']) ? (int)$data['ordering'] : null,
            $data['source_id'] ?? null
        );
    }

    public function toArray(): array
    {
        $out = [
            'name' => $this->name,
        ];
        if ($this->id !== null) {
            $out['id'] = $this->id;
        }
        if ($this->siteUrl !== null) {
            $out['site_url'] = $this->siteUrl;
        }
        if ($this->logoFilename !== null) {
            $out['logo_filename'] = $this->logoFilename;
        }
        if ($this->ordering !== null) {
            $out['ordering'] = $this->ordering;
        }
        if ($this->sourceId !== null) {
            $out['source_id'] = $this->sourceId;
        }
        return $out;
    }
}

<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Model;

class Producer
{
    public ?int $id;
    public string $name;

    public function __construct(string $name, ?int $id = null)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? $data['title'] ?? '',
            isset($data['id']) ? (int)$data['id'] : null
        );
    }

    public function toArray(): array
    {
        $out = ['name' => $this->name];
        if ($this->id !== null) {
            $out['id'] = $this->id;
        }
        return $out;
    }
}

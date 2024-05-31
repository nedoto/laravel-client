<?php

declare(strict_types=1);

namespace Nedoto;

use DateTimeImmutable;

class Configuration
{
    private string $slug;

    private string $type;

    private string|int|float|bool $value;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $slug,
        string $type,
        string|int|float|bool $value,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->slug = $slug;
        $this->type = $type;
        $this->value = $value;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string|int|float|bool
    {
        return $this->value;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

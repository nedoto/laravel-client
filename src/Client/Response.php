<?php

declare(strict_types=1);

namespace Nedoto\Client;

use Nedoto\Configuration;

class Response
{
    private ?Configuration $configuration;

    /**
     * @var array<int, string>|array<string, array<string, array<int, string>>>
     */
    private array $errors;

    private int $status;

    /**
     * @param  array<int, string>|array<string, array<string, array<int, string>>>  $errors
     */
    public function __construct(
        ?Configuration $configuration,
        array $errors,
        int $status
    ) {
        $this->configuration = $configuration;
        $this->errors = $errors;
        $this->status = $status;
    }

    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    /**
     * @return array<int, string>|array<string, array<string, array<int, string>>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function failed(): bool
    {
        return ! empty($this->errors) || $this->configuration === null;
    }
}

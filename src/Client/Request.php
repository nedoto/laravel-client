<?php

declare(strict_types=1);

namespace Nedoto\Client;

use InvalidArgumentException;

class Request
{
    private string $configurationName;

    public function __construct(
        string $configurationName
    ) {
        if (empty($configurationName)) {
            throw new InvalidArgumentException('$configurationName cannot be an empty string.');
        }

        $this->configurationName = $configurationName;
    }

    public function getConfigurationName(): string
    {
        return $this->configurationName;
    }
}

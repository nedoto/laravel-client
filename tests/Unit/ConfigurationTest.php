<?php

declare(strict_types=1);

namespace Nedoto\Tests\Unit;

use DateTimeImmutable;
use Nedoto\Configuration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Configuration::class)]
class ConfigurationTest extends TestCase
{
    public function testGetters(): void
    {
        $sut = new Configuration(
            'name',
            'type',
            'value',
            new DateTimeImmutable('2023-01-01 12:54:00'),
            new DateTimeImmutable('2023-01-02 12:54:00'),
        );

        $this->assertEquals('name', $sut->getSlug());
        $this->assertEquals('type', $sut->getType());
        $this->assertEquals('value', $sut->getValue());
        $this->assertEquals(new DateTimeImmutable('2023-01-01 12:54:00'), $sut->getCreatedAt());
        $this->assertEquals(new DateTimeImmutable('2023-01-02 12:54:00'), $sut->getUpdatedAt());
    }
}

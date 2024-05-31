<?php

declare(strict_types=1);

namespace Nedoto\Tests\Unit\Client;

use InvalidArgumentException;
use Nedoto\Client\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Request::class)]
class RequestTest extends TestCase
{
    public function testConstructorMustThrowExceptionWithEmptyConfigurationName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$configurationName cannot be an empty string.');

        new Request('');
    }

    public function testGetters(): void
    {
        $sut = new Request('config-name');

        $this->assertEquals('config-name', $sut->getConfigurationName());
    }
}

<?php

declare(strict_types=1);

namespace Nedoto\Tests\Unit\Client;

use DateTimeImmutable;
use Illuminate\Http\Response as HttpResponse;
use Nedoto\Client\Response;
use Nedoto\Configuration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Response::class)]
class ResponseTest extends TestCase
{
    public function testGettersMustReturnEmptyValues(): void
    {
        $sut = new Response(null, [], HttpResponse::HTTP_BAD_REQUEST);

        $this->assertEquals(null, $sut->getConfiguration());
        $this->assertEquals([], $sut->getErrors());
        $this->assertTrue($sut->failed());
    }

    public function testGettersMustReturnValidConfigurationWithoutErrors(): void
    {
        $config = new Configuration(
            'name',
            'type',
            'value',
            new DateTimeImmutable('2023-01-01 12:54:00'),
            new DateTimeImmutable('2023-01-02 12:54:00'),
        );

        $sut = new Response($config, [], HttpResponse::HTTP_OK);

        $this->assertEquals($config, $sut->getConfiguration());
        $this->assertEquals([], $sut->getErrors());
        $this->assertFalse($sut->failed());
    }

    public function testGettersMustReturnEmptyConfigurationWithErrors(): void
    {
        $sut = new Response(null, ['error'], HttpResponse::HTTP_BAD_REQUEST);

        $this->assertEquals(null, $sut->getConfiguration());
        $this->assertEquals(['error'], $sut->getErrors());
        $this->assertTrue($sut->failed());
    }
}

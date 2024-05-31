<?php

declare(strict_types=1);

namespace Nedoto\Tests\Unit\Client;

use DateTimeImmutable;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Response as HttpResponse;
use Nedoto\Client\NedotoClient;
use Nedoto\Client\Request;
use Nedoto\Client\Response;
use Nedoto\Configuration;
use Nedoto\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(NedotoClient::class)]
class NedotoClientTest extends TestCase
{
    private Factory $httpClient;

    private NedotoClient $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new Factory();
    }

    /**
     * @param  array<string, array<string, array<string, string|int|float|bool>>>  $validResponse
     */
    #[DataProvider('validApiResponseDataProvider')]
    public function testGetMustReturnCorrectNedotoResponseAndConfiguration(array $validResponse): void
    {
        $this->httpClient->fake([
            '*' => $this->httpClient::response($validResponse, HttpResponse::HTTP_OK),
        ]);

        $this->sut = new NedotoClient(
            $this->httpClient,
            'api-key'
        );

        $response = $this->sut->get(new Request('test'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(Configuration::class, $response->getConfiguration());
        $this->assertIsArray($response->getErrors());
        $this->assertCount(0, $response->getErrors());
        $this->assertFalse($response->failed());
        $this->assertEquals(HttpResponse::HTTP_OK, $response->getStatus());

        $configuration = $response->getConfiguration();
        $this->assertEquals($validResponse['variable']['data']['slug'], $configuration->getSlug());
        $this->assertEquals($validResponse['variable']['data']['type'], $configuration->getType());
        $this->assertEquals($validResponse['variable']['data']['value'], $configuration->getValue());
        $this->assertEquals($validResponse['variable']['data']['created_at'], $configuration->getCreatedAt()->format(DateTimeImmutable::ATOM));
        $this->assertEquals($validResponse['variable']['data']['updated_at'], $configuration->getUpdatedAt()->format(DateTimeImmutable::ATOM));
    }

    #[DataProvider('httpErrorsDataProvider')]
    public function testGetMustReturnNedotoResponseWithErrorsDueToHttpError(int $httpError): void
    {
        $this->httpClient->fake([
            '*' => $this->httpClient::response(['error-response'], $httpError),
        ]);

        $this->sut = new NedotoClient(
            $this->httpClient,
            'api-key'
        );

        $response = $this->sut->get(new Request('test'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertNull($response->getConfiguration());
        $this->assertIsArray($response->getErrors());
        $this->assertCount(1, $response->getErrors());
        $this->assertTrue($response->failed());
        $this->assertEquals($httpError, $response->getStatus());
    }

    /**
     * @param  array<int, array<int|string, array<string, array<string, string|string[]|\stdClass|null|int|float|bool>>|string>>  $invalidApiResponse
     * @param  string[]  $expectedErrors
     */
    #[DataProvider('invalidApiResponseDataProvider')]
    public function testGetWithInvalidApiResponsePayloadMustReturnNedotoResponseWithErrors(
        array $invalidApiResponse,
        array $expectedErrors
    ): void {
        $this->httpClient->fake([
            '*' => $this->httpClient::response($invalidApiResponse, HttpResponse::HTTP_OK),
        ]);

        $this->sut = new NedotoClient(
            $this->httpClient,
            'api-key'
        );

        $response = $this->sut->get(new Request('test'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertNull($response->getConfiguration());
        $this->assertIsArray($response->getErrors());
        $this->assertCount(count($expectedErrors), $response->getErrors());

        foreach ($expectedErrors as $index => $expectedError) {
            $this->assertEquals($expectedError, $response->getErrors()[$index]);
        }
    }

    /**
     * @return iterable<array<int, array<string, array<string, array<string, string|int|float|bool>>>>>
     */
    public static function validApiResponseDataProvider(): iterable
    {
        yield 'valid response' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'string',
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
        ];

        yield 'valid response 1' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'int',
                        'value' => 1,
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
        ];

        yield 'valid response 2' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'float',
                        'value' => 1.1,
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
        ];

        yield 'valid response 3' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'bool',
                        'value' => true,
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
        ];

        yield 'valid response 4' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'code',
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
        ];

        yield 'valid response 5' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'html',
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return iterable<array<int, int>>
     */
    public static function httpErrorsDataProvider(): iterable
    {
        yield 'http error 400' => [HttpResponse::HTTP_BAD_REQUEST];
        yield 'http error 401' => [HttpResponse::HTTP_UNAUTHORIZED];
        yield 'http error 403' => [HttpResponse::HTTP_FORBIDDEN];
        yield 'http error 404' => [HttpResponse::HTTP_NOT_FOUND];
        yield 'http error 405' => [HttpResponse::HTTP_METHOD_NOT_ALLOWED];
        yield 'http error 500' => [HttpResponse::HTTP_INTERNAL_SERVER_ERROR];
    }

    /**
     * @return iterable<array<int, array<int|string, array<string, array<string, string|string[]|\stdClass|null|int|float|bool>>|string>>>
     */
    public static function invalidApiResponseDataProvider(): iterable
    {
        yield 'missing variable.data.slug' => [
            [
                'variable' => [
                    'data' => [
                        'type' => 'int',
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            ['The variable.data.slug field is required.'],
        ];

        yield 'missing variable.data.type' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            ['The variable.data.type field is required.'],
        ];

        yield 'missing variable.data.value' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'int',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            ['The variable.data.value field is required.'],
        ];

        yield 'missing variable.data.created_at' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'int',
                        'value' => 'test-value',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            ['The variable.data.created at field is required.'],
        ];

        yield 'missing variable.data.updated_at' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'int',
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                    ],
                ],
            ],
            ['The variable.data.updated at field is required.'],
        ];

        yield 'invalid variable.data.slug' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 1,
                        'type' => 'int',
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            ['The variable.data.slug field must be a string.'],
        ];

        yield 'invalid variable.data.type' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 1,
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            [
                'The variable.data.type field must be a string.',
                'The selected variable.data.type is invalid.',
            ],
        ];

        yield 'invalid variable.data.type string' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'invalid-string',
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            [
                'The selected variable.data.type is invalid.',
            ],
        ];

        yield 'invalid variable.data.value' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'int',
                        'value' => ['test-value'],
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            [
                'The variable.data.value must be one of these types: int, float, bool, string "array" given.',
            ],
        ];

        yield 'invalid variable.data.value 1' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'int',
                        'value' => new \stdClass(),
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            [
                'The variable.data.value field is required.',
            ],
        ];

        yield 'invalid variable.data.value 2' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'int',
                        'value' => null,
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            [
                'The variable.data.value field is required.',
            ],
        ];

        yield 'invalid variable.data.created_at' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'int',
                        'value' => 'test-value',
                        'created_at' => 'invalid-date',
                        'updated_at' => '2024-04-08T21:08:23+00:00',
                    ],
                ],
            ],
            [
                'The variable.data.created at field must be a valid date.',
            ],
        ];

        yield 'invalid variable.data.updated_at' => [
            [
                'variable' => [
                    'data' => [
                        'slug' => 'test-slug',
                        'type' => 'int',
                        'value' => 'test-value',
                        'created_at' => '2024-04-07T21:08:23+00:00',
                        'updated_at' => 'invalid-date',
                    ],
                ],
            ],
            [
                'The variable.data.updated at field must be a valid date.',
            ],
        ];
    }
}

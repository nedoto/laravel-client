<?php

declare(strict_types=1);

namespace Nedoto\Client;

use DateTimeImmutable;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Nedoto\Configuration;

class NedotoClient
{
    private const NEDOTO_ENDPOINT = 'https://app.nedoto.com/api/var/get';

    private Factory $httpClient;

    private string $apiKey;

    public function __construct(
        Factory $httpClient,
        string $apiKey
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function get(string $slug): Response
    {
        if (mb_strlen($slug) === 0) {
            throw new InvalidArgumentException('$slug cannot be an empty string.');
        }

        $response = $this->getFromApi($slug);

        if ($response->failed()) {
            return new Response(
                null,
                $response->json() ?? [],
                $response->status()
            );
        }

        $errors = $this->validateResponse($response);

        $configuration = null;

        if (empty($errors)) {
            $configuration = $this->buildConfiguration($response);
        }

        return new Response(
            $configuration,
            $errors,
            $response->status()
        );
    }

    private function getFromApi(string $slug): HttpResponse
    {
        return $this->httpClient
            ->withHeaders([
                'X-Api-Key' => $this->apiKey,
            ])
            ->get(
                sprintf(
                    '%s/%s',
                    self::NEDOTO_ENDPOINT,
                    trim($slug)
                )
            );
    }

    /**
     * @return array<int, string>
     */
    private function validateResponse(HttpResponse $response): array
    {
        $validator = Validator::make(
            $response->json(),
            $this->getValidationRules()
        );

        return $validator->fails() ? $validator->errors()->all() : [];
    }

    private function buildConfiguration(HttpResponse $response): Configuration
    {
        $body = $response->json();

        $createdAt = DateTimeImmutable::createFromFormat(
            \DateTime::ATOM,
            $body['variable']['data']['created_at']
        );

        $updatedAt = DateTimeImmutable::createFromFormat(
            \DateTime::ATOM,
            $body['variable']['data']['updated_at']
        );

        if ($createdAt === false) {
            throw new InvalidArgumentException('$createdAt must be a valid DateTimeImmutable value.');
        }

        if ($updatedAt === false) {
            throw new InvalidArgumentException('$updatedAt must be a valid DateTimeImmutable value.');
        }

        return new Configuration(
            $body['variable']['data']['slug'],
            $body['variable']['data']['type'],
            $body['variable']['data']['value'],
            $createdAt,
            $updatedAt
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function getValidationRules(): array
    {
        return [
            'variable' => 'required|array:data',
            'variable.data' => 'required|array:slug,type,value,created_at,updated_at',
            'variable.data.slug' => 'required|string',
            'variable.data.type' => 'required|string|in:string,int,float,bool,code,html',
            'variable.data.created_at' => 'required|date',
            'variable.data.updated_at' => 'required|date',
            'variable.data.value' => ['required', function (string $attribute, mixed $value, \Closure $fail) {
                if (
                    ! (is_int($value) ||
                        is_float($value) ||
                        is_bool($value) ||
                        is_string($value))
                ) {
                    $fail(
                        sprintf(
                            'The variable.data.value must be one of these types: int, float, bool, string "%s" given.',
                            gettype($value)
                        )
                    );
                }
            }],
        ];
    }
}

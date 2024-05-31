<?php

declare(strict_types=1);

namespace Nedoto;

use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Nedoto\Client\NedotoClient;

class NedotoServiceProvider extends ServiceProvider
{
    private const CONFIG_API_KEY = 'nedoto.api-key';

    public function boot(): void
    {
        $this->setUpConfig();
    }

    public function register(): void
    {
        $this->validateApiKey();

        $this->app->bind(NedotoClient::class, function () {
            return new NedotoClient(
                $this->app->get(Factory::class),
                config(self::CONFIG_API_KEY)
            );
        });
    }

    protected function setUpConfig(): void
    {
        $source = sprintf(
            '%s/../config/nedoto.php',
            __DIR__
        );

        $this->publishes(
            [$source => config_path('nedoto.php')],
            ['nedoto-laravel-client']
        );
    }

    private function validateApiKey(): void
    {
        $apiKey = config(self::CONFIG_API_KEY);

        if (empty($apiKey)) {
            throw new InvalidArgumentException(<<<'STRING'
NEDOTO_API_KEY is not set in your .env file.
Please read the documentation at https://github.com/nedoto/laravel-client?tab=readme-ov-file#installation and set it in your .env file.
STRING
            );
        }
    }
}

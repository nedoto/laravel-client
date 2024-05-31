<?php

declare(strict_types=1);

namespace Nedoto;

use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;
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
}

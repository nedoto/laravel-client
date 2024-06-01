# Nedoto Laravel Client

[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE) [![CI suite](https://github.com/nedoto/laravel-client/actions/workflows/ci.yml/badge.svg)](https://github.com/nedoto/laravel-client/actions/workflows/ci.yml)

Laravel package to connect to Nedoto API.

Nedoto website: https://app.nedoto.com

## Installation

This package requires `PHP >=8.1` and is built for `Laravel >=11.x` or higher.

Installation with Composer:

```shell
composer require nedoto/laravel-client ^1.0
```

Publish the package configuration:

```shell
php artisan vendor:publish --tag=nedoto-laravel-client
```

Open your `.env` configuration file and configure the `NEDOTO_API_KEY` using your api-key created
at [https://app.nedoto.com/api-keys](https://app.nedoto.com/api-keys).

```dotenv
NEDOTO_API_KEY=<YOUR PROJECT ENV API-KEY>
```

## Usage

To retrieve your configuration from Nedoto API you should add a reference to the `Nedoto\Client\NedotoClient` to you
class and then
use the Client to retrieve your configuration with the unique key.

The `$response` object is of type `Nedoto\Client\Response` and with it, you can retrieve the `Nedoto\Configuration`
object.  
From the Configuration object you can access your configuration value calling the `getValue()` method.

```php
<?php

    declare(strict_types=1);

    namespace YourNamespace;

    use Nedoto\Client\NedotoClient;

    final class MyClass
    {
        private NedotoClient $nedotoClient;
        
        public function __construct(NedotoClient $nedotoClient)
        {
            $this->nedotoClient = $nedotoClient;
        }
    
        public function retrieveNedotoConfiguration(): string {
        
            $request = new Request('my-configuration-key');
            
            $response = $this->nedotoClient->get($request);
            
            return $response->getConfiguration()->getValue();
        }
    }
```

## The Nedoto Response

After you retrieve the configuration with the Nedoto client, you'll receive a `Nedoto\Client\Response`.

### Understand if everything is ok

To understand if everything went fine after retrieving your configuration, you should use the `getStatus()` method.  
It will return a standard HTTP status.

```php
$response->getStatus(); // ex. 200
```

Alternatively you could you use the `failed()` method that will inform you if there was a failure by returning a boolean
value if the HTTP status is different from 200 (HTTP OK).

```php
$response->failed(); // ex. true (if HTTP status is different from 200)
```

### Understand the errors

After checking if the status of the response you may want to understand which errors happened during the API request.  
For this you could use the `getErrors()` method.

```php
$response->getErrors();
```

`getErrors()` method will return an array of reasons explaining why:

```php
[
    0 => 'Error 1',
    1 => 'Error 2',
    1 => 'Error 3',
    // ...
]
```

### Retrieve the Configuration

To retrieve your configuration value you must use the `getConfiguration()` method.

```php
$response->getConfiguration(); // return Nedoto\Configuration
```

## Reading you configuration

After you have your Nedoto\Configuration object you can access different information.

### Retrieve the value

Probably the most important thing to read in your configuration is actually the value.  
To do that you should simply use the `getValue()` method.

```php
$configuration->getValue(); // ex: 100
```

### Understand the type

Since Nedoto gives you the possibility to define the `type` of your configuration or variable, in order to correctly parse
your configuration or variable, you should use the `getType()` method.

```php
$configuration->getType(); // ex. integer
```

You don't need to cast the value to the type you want, since the `getValue()` method will return the value already casted.

### Access the creation date

By using the `getCreatedAt()` you can access the creation `DateTime` of the configuration.

```php
$configuration->getCreatedAt(); // ex. DateTimeImmutable
```

### Access the update date

By using the `getUpdatedAt()` you can access the update `DateTime` of the configuration.

```php
$configuration->getCreatedAt(); // ex. DateTimeImmutable
```

# Want to improve something?

Please feel free to open a PR if you want to improve something on this repository.

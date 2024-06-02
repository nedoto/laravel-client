# Nedoto Laravel Client

[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE) [![CI suite](https://github.com/nedoto/laravel-client/actions/workflows/ci.yml/badge.svg)](https://github.com/nedoto/laravel-client/actions/workflows/ci.yml)

A Laravel package to connect to the Nedoto API.

References:

- Nedoto website: https://nedoto.com
- Nedoto app website: https://app.nedoto.com
- Nedoto documentation website: https://docs.nedoto.com

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

Open your `.env` file and add the `NEDOTO_API_KEY` env var using your api-key created
at [https://app.nedoto.com/api-keys](https://app.nedoto.com/api-keys).

```dotenv
NEDOTO_API_KEY=<YOUR PROJECT ENV API-KEY>
```

_Note:_ before start retrieving your configuration, be sure to enable the Project, Environment, Variable and Api key
in https://app.nedoto.com.

## Usage

To retrieve your configuration or variable from Nedoto API you should add a reference to
the `Nedoto\Client\NedotoClient` to you class and then use the Client to retrieve your configuration with the unique key
that is the Variable `slug`.

As shown in the example below, the `$response` object is of type `Nedoto\Client\Response` and with it, you can retrieve the `Nedoto\Configuration`
object.  
From the Configuration object you can access your configuration value calling the `getValue()` method.

```php
<?php

    declare(strict_types=1);

    namespace YourNamespace;

    // import the required namespaces 
    use Nedoto\Client\NedotoClient;
    use Nedoto\Client\Request;

    final class MyClass
    {
        private NedotoClient $nedotoClient;
        
        public function __construct(NedotoClient $nedotoClient) // 1. inject Nedoto Client
        {
            $this->nedotoClient = $nedotoClient;
        }
    
        public function retrieveNedotoConfiguration(): string {
        
            $request = new Request('my-configuration-key'); // 2. create a new Nedoto Request with the slug you want to retrieve as a mandatory parameter
            
            $response = $this->nedotoClient->get($request); // 3. call the "get()" method on the Nedoto Client
            
            return $response->getConfiguration()->getValue(); // 4. retrieve your value from the Configuration object
        }
    }
```

## The Nedoto Response

After the call to the `get()` method, you'll receive a `Nedoto\Client\Response`.

### Understand if the Nedoto response is ok

To understand if everything went fine after retrieving your configuration, you should use the `getStatus()` method.  
It will return a standard HTTP status code.

```php
$response->getStatus(); // ex. 200
```

Alternatively you could you use the `failed()` method that will inform you if there was a failure by returning
a `boolean`
value if the HTTP status code is different from `200` (HTTP OK).

```php
$response->failed(); // ex. true (if HTTP status code is different from 200)
```

### Understand the errors

After checking if the status of the response you may want to understand which errors happened during the API request.  
For this you could use the `getErrors()` method.

```php
$response->getErrors();
```

The `getErrors()` method will return an array of reasons explaining what's wrong with the request.

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

After you have your `Nedoto\Configuration` object you can access different information explained below.

### Retrieve the value

Probably the most important thing to read in your configuration is actually the value.  
To do that you should simply use the `getValue()` method.

```php
$configuration->getValue(); // ex: it returns the value of the configuration defined in one of the variables at https://app.nedoto.com/variables
```

_Note:_ the `getValue()` method will return the value already casted to the type you defined in the variable
in https://app.nedoto.com/variables.

### Understand the type

Since Nedoto gives you the possibility to define the `type` of your configuration or variable you can retrieve it using
the `getType()` method.

```php
$configuration->getType(); // ex. integer
```

_Note:_ You don't need to cast the value to the type you want, since the `getValue()` method will return the value
already
casted.

### Access the creation date

By using the `getCreatedAt()` you can access the creation date of the configuration.

```php
$configuration->getCreatedAt(); // DateTimeImmutable
```

### Access the update date

By using the `getUpdatedAt()` you can access the update date of the configuration.

```php
$configuration->getUpdatedAt(); // ex. DateTimeImmutable
```

# Want to improve something?

Please feel free to open a PR if you want to improve something on this repository.

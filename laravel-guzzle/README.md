# Guzzle wrapper for HTTP Requests

A simple wrapper for guzzle http requests, to make the http calls using headers easier

## Install

Install this package using composer

```js
composer require bitstone/guzzle-wrapper

or

add the library to your composer.json file:

"bitstone/guzzle-wrapper": "~1.0"
```

Then you need to update the service providers array, in your config/app.php

```php
'Bitstone\\GuzzleWrapper\\HttpServiceProvider'
```

And the alias for the facade

```php
'Http' => 'Bitstone\\GuzzleWrapper\\Dispatcher'
```

## Usage

```php
Http::request('GET', http://example.com/api/v1/users', ['role' => 'admin'], ['Content-Type' => 'application/json']);
```

Or

```php
Http::request('GET', http://example.com/api/v1/users?role=admin', [], ['Content-Type' => 'application/json']);
```

```php
Http::request('POST', http://example.com/api/v1/users', ['option' => 'value'], ['Content-Type' => 'application/json', 'Accept' => 'application/json']);
```

But it's possible to call the specific http method as well:

```php
Http::get('http://example.com/api/v1/users');
```

```php
Http::post('http://example.com/api/v1/users/1', ['option' => 'value'], ['Accept' => 'application/json']);
```

```php
Http::put('http://example.com/api/v1/users/1', ['option' => 'another value'], ['Accept' => 'application/json']);
```

```php
Http::delete('http://example.com/api/v1/users/1');
```

```php
Http::head('http://example.com/api/v1/users/1');
```


# PHP Wsify Client
[![Travis CI](https://img.shields.io/travis/eislambey/php-wsify)](https://travis-ci.org/eislambey/php-wsify)
[![Codecov](https://img.shields.io/codecov/c/github/eislambey/php-wsify)](https://codecov.io/gh/eislambey/php-wsify)

PHP Client for [Wsify](https://github.com/alash3al/wsify) realtime messaging server.

**What is Wsify?**
> Just a tiny, simple and realtime pub/sub messaging service

For more information please see official repo: https://github.com/alash3al/wsify

## Installation
	composer require eislambey/php-wsify

## Examples

**Publish a message to all subscribers**
```php
<?php
$uri = 'http://localhost:4040/publish';
$publisher = new \Wsify\Publisher($uri);

$publisher->publish('channel_name', 'a message to sent');
```

**Send a message to an user**
```php
<?php
$uri = 'http://localhost:4040/publish';
$publisher = new \Wsify\Publisher($uri);

$publisher->publish('channel_name', 'a message to sent', ['user_key']);
```

**Using webhooks**
```php
<?php
$events = new \Wsify\Events();

$events->onConnect(function (object $payload){
    // `$payload->action` and `$payload->key` available
});

$events->onDisconnect(function (object $payload){
    // `$payload->action` and `$payload->key` available
});

$events->onSubscribe(function (object $payload){
    // `$payload->action`, `$payload->channel` and `$payload->key` available
});

$events->onUnsubscribe(function (object $payload){
    // `$payload->action`, `$payload->channel` and `$payload->key` available
});

$events->listen();
```

## Test
	WSIFY_PUBLISH_URL='http://localhost:4040/publish' composer test 

## License
The MIT License. See [LICENSE](./LICENSE)
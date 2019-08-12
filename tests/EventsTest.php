<?php

use Wsify\Events;

class EventsTest extends \PHPUnit\Framework\TestCase
{
    public function testOnConnect()
    {
        $input = '{"action": "connect", "key": "client1"}';
        $events = new Events($input);

        $counter = 0;
        $events->onConnect(function ($payload) use (&$counter) {
            $this->assertSame($payload->key, "client1");
            $this->assertSame($payload->action, "connect");
            $counter++;
        });
        $events->onConnect(function ($payload) use (&$counter) {
            $this->assertSame($payload->key, "client1");
            $this->assertSame($payload->action, "connect");
            $counter++;
        });

        $listener = function () use (&$counter) {
            $counter++;
        };
        $events->onSubscribe($listener);
        $events->onDisconnect($listener);
        $events->onUnsubscribe($listener);

        $events->listen();
        $this->assertSame($counter, 2);
    }

    public function testOnDisconnect()
    {
        $input = '{"action": "disconnect", "key": "client1"}';
        $events = new Events($input);

        $counter = 0;
        $events->onDisconnect(function ($payload) use (&$counter) {
            $this->assertSame("client1", $payload->key);
            $this->assertSame("disconnect", $payload->action);
            $counter++;
        });
        $events->onDisconnect(function ($payload) use (&$counter) {
            $this->assertSame("client1", $payload->key);
            $this->assertSame("disconnect", $payload->action);
            $counter++;
        });

        $listener = function () use (&$counter) {
            $counter++;
        };
        $events->onSubscribe($listener);
        $events->onConnect($listener);
        $events->onUnsubscribe($listener);

        $events->listen();
        $this->assertSame(2, $counter);
    }

    public function testOnSubscribe()
    {
        $action = 'subscribe';
        $channel = 'public';
        $key = 'clientKey';
        $input = "{\"action\":\"$action\",\"channel\":\"$channel\",\"key\":\"$key\"}";
        $events = new Events($input);

        $counter = 0;
        $events->onSubscribe(function ($payload) use (&$counter, $action, $channel, $key) {
            $this->assertSame($payload->key, $key);
            $this->assertSame($payload->action, $action);
            $this->assertSame($payload->channel, $channel);
            $counter++;
        });
        $events->onSubscribe(function ($payload) use (&$counter, $action, $channel, $key) {
            $this->assertSame($payload->key, $key);
            $this->assertSame($payload->action, $action);
            $this->assertSame($payload->channel, $channel);
            $counter++;
        });

        $listener = function () use (&$counter) {
            $counter++;
        };
        $events->onConnect($listener);
        $events->onDisconnect($listener);
        $events->onUnsubscribe($listener);

        $events->listen();
        $this->assertSame($counter, 2);
    }

    public function testOnUnsubscribe()
    {
        $action = 'unsubscribe';
        $channel = 'public';
        $key = 'clientKey';
        $input = "{\"action\":\"$action\",\"channel\":\"$channel\",\"key\":\"$key\"}";
        $events = new Events($input);

        $counter = 0;
        $events->onUnsubscribe(function ($payload) use (&$counter, $action, $channel, $key) {
            $this->assertSame($payload->key, $key);
            $this->assertSame($payload->action, $action);
            $this->assertSame($payload->channel, $channel);
            $counter++;
        });
        $events->onUnsubscribe(function ($payload) use (&$counter, $action, $channel, $key) {
            $this->assertSame($payload->key, $key);
            $this->assertSame($payload->action, $action);
            $this->assertSame($payload->channel, $channel);
            $counter++;
        });

        $listener = function () use (&$counter) {
            $counter++;
        };
        $events->onConnect($listener);
        $events->onDisconnect($listener);
        $events->onSubscribe($listener);

        $events->listen();
        $this->assertSame($counter, 2);
    }

    public function testBadInput()
    {
        $events = new Events('Bad string');

        $this->expectExceptionMessage('Syntax error');
        $this->expectException(JsonException::class);
        $events->listen();
    }

    public function testBadJson()
    {
        $events = new Events('{"action":"connected"}');
        $this->expectExceptionMessage('Action name empty or not supported');
        $this->expectException(\Exception::class);
        $events->listen();
    }
}

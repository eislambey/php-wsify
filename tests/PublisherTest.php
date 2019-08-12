<?php
$dir = dirname(__FILE__);
require_once $dir . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class PublisherTest extends TestCase
{
    /**
     * @var \Wsify\Publisher
     */
    private $publisher;

    public function setUp(): void
    {
        $url = 'http://localhost:4040/publish';
        $this->publisher = new \Wsify\Publisher($url);
    }

    public function testPublish()
    {
        $channel = 'public';
        $payload = 'Example message';
        $this->assertTrue($this->publisher->publish($channel, $payload));
    }

    public function testBadPublishUrl()
    {
        $url = 'http://localhost:4040/publissh';
        $publisher = new \Wsify\Publisher($url);

        $channel = 'public';
        $payload = 'Example message';
        $this->expectExceptionMessageRegExp('/Unable to publish: (.*?)/');
        $this->expectException(RuntimeException::class);
        $publisher->publish($channel, $payload);
    }

    public function testBadResponse()
    {
        $url = 'http://httpbin.org/post';
        $publisher = new \Wsify\Publisher($url);

        $channel = 'public';
        $payload = 'Example message';
        $this->expectExceptionMessage('Success property is not defined in response.');
        $this->expectException(Exception::class);
        $publisher->publish($channel, $payload);
    }

    public function testEmptyPayload()
    {
        $channel = 'public';
        $payload = '';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payload should not be empty.');
        $this->publisher->publish($channel, $payload);
    }

}
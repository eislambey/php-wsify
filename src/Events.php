<?php
declare(strict_types=1);

namespace Wsify;

class Events
{
    /**
     * @var string
     */
    private $input;

    /**
     * @var array
     */
    private $listeners = [
        'connect' => [],
        'disconnect' => [],
        'subscribe' => [],
        'unsubscribe' => [],
    ];

    public function __construct(string $input = null)
    {
        if ($input) {
            $this->input = $input;
        } else {
            $this->input = file_get_contents("php://input");
        }
    }

    public function onConnect(callable $listener): void
    {
        $this->listeners['connect'][] = $listener;
    }

    public function onDisconnect(callable $listener): void
    {
        $this->listeners['disconnect'][] = $listener;
    }

    public function onSubscribe(callable $listener): void
    {
        $this->listeners['subscribe'][] = $listener;
    }

    public function onUnsubscribe(callable $listener): void
    {
        $this->listeners['unsubscribe'][] = $listener;
    }

    /**
     * @throws \JsonException
     */
    public function listen(): void
    {
        $payload = json_decode($this->input);

        if (null === $payload) {
            throw new \JsonException(json_last_error_msg());
        }
        if (!isset($payload->action) || !isset($this->listeners[$payload->action])) {
            throw new \Exception("Action name empty or not supported");
        }
        $listeners = $this->listeners[$payload->action];
        foreach ($listeners as $listener) {
            $listener($payload);
        }
    }
}

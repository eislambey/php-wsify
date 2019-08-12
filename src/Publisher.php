<?php
declare(strict_types=1);

namespace Wsify;

class Publisher
{
    private $publishUrl;

    public function __construct(string $publishUrl)
    {
        $this->publishUrl = $publishUrl;
    }

    /**
     * @param string $channel
     * @param string $payload
     * @param array $to
     * @throws \RuntimeException
     * @throws \Exception
     * @return bool
     */
    public function publish(string $channel, string $payload, array $to = []): bool
    {
        if (empty($payload)) {
            throw new \InvalidArgumentException('Payload should not be empty.');
        }
        $data = [
            'channel' => $channel,
            'payload' => $payload,
            'to' => $to,
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => json_encode($data),
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($this->publishUrl, false, $context);

        if ($response === false) {
            $error = error_get_last()['message'] ?? 'unknown error';
            throw new \RuntimeException("Unable to publish: $error");
        }

        $json = json_decode($response);
        if (!isset($json->success)) {
            throw new \Exception('Success property is not defined in response.');
        }

        return $json->success;
    }
}

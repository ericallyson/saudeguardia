<?php

namespace App\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

class SubscriptionException extends RuntimeException
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(string $message, protected array $context = [], int $code = 0)
    {
        parent::__construct($message, $code);
    }

    public static function failed(string $operation, Response $response): self
    {
        return new self(
            sprintf('Subscription API call failed for [%s].', $operation),
            [
                'status' => $response->status(),
                'body' => $response->json(),
            ],
            $response->status()
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }
}

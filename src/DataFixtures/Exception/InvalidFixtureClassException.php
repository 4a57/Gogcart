<?php

namespace App\DataFixtures\Exception;

use Throwable;

class InvalidFixtureClassException extends \Exception
{
    const MESSAGE = 'Class %s is not an fixture';

    public function __construct(string $class, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = sprintf(self::MESSAGE, $class);
    }
}

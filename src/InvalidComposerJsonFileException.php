<?php

declare(strict_types=1);

namespace Horde\Victim;

use RuntimeException;
use Throwable;

class InvalidComposerJsonFileException extends RuntimeException
{
    public function __construct(
        string $message = 'Invalid composer.json file',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

<?php

declare(strict_types=1);

namespace Rahul900day\Gpt3Encoder\Exceptions;

use Exception;

class FileNotFoundException extends Exception
{
    public function __construct(string $path)
    {
        parent::__construct("File [{$path}] failed to load.");
    }
}

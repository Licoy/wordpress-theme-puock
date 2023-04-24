<?php

declare(strict_types=1);

namespace Rahul900day\Gpt3Encoder\Support;

use Exception;
use Rahul900day\Gpt3Encoder\Exceptions\FileNotFoundException;

class File
{
    public static function get(string $path): string
    {
        try {
            $file = file_get_contents($path);
        } catch (Exception $err) {
            $file = false;
        }

        if ($file === false) {
            throw new FileNotFoundException($path);
        }

        return $file;
    }
}

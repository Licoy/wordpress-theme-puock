<?php

declare(strict_types=1);

namespace Rahul900day\Gpt3Encoder;

use Rahul900day\Gpt3Encoder\Support\File;

class TokenEncoder
{
    protected static array $encoded;

    public static function encode(string $bpe_token): array
    {
        $segments = explode(' ', $bpe_token);
        $tokens = [];

        foreach ($segments as $segment) {
            $tokens[] = self::getEncoded()[$segment];
        }

        return $tokens;
    }

    public static function decode(array $tokens): string
    {
        $text = '';
        foreach ($tokens as $token) {
            $text .= array_search($token, self::getEncoded());
        }

        return $text;
    }

    protected static function getEncoded(): array
    {
        if (isset(self::$encoded)) {
            return self::$encoded;
        }

        self::$encoded = (array) json_decode(File::get(__DIR__.'/../data/encoder.json'), true, 512, JSON_THROW_ON_ERROR);

        return self::$encoded;
    }
}

<?php

declare(strict_types=1);

namespace Rahul900day\Gpt3Encoder;

class Character
{
    protected static array $characters;

    public static function fromBytes(array $bytes): string
    {
        $word = '';
        foreach ($bytes as $byte) {
            $word .= self::getCharacters()[$byte];
        }

        return $word;
    }

    public static function toBytes(string $word): array
    {
        $bytes = [];
        $characters = mb_str_split($word);

        foreach ($characters as $character) {
            $bytes[] = array_search($character, self::getCharacters());
        }

        return $bytes;
    }

    protected static function getCharacters(): array
    {
        if (isset(self::$characters)) {
            return self::$characters;
        }

        $asciiByteRange = range(mb_ord('!'), mb_ord('~'));
        $latinByteRange = range(mb_ord('¡'), mb_ord('¬'));
        $extendedLatinByteRange = range(mb_ord('®'), mb_ord('ÿ'));

        $byteValues = [...$asciiByteRange, ...$latinByteRange, ...$extendedLatinByteRange];
        $unicodeValues = $byteValues;
        $unicodeValueCounter = 0;

        for ($byte = 0; $byte < 2 ** 8; $byte++) {
            if (! in_array($byte, $byteValues)) {
                $byteValues[] = $byte;
                $unicodeValues[] = 2 ** 8 + $unicodeValueCounter;
                $unicodeValueCounter++;
            }
        }

        $unicodeCharacters = array_map('mb_chr', $unicodeValues);

        $charactersMap = [];
        foreach ($byteValues as $i => $byteValue) {
            $charactersMap[$byteValue] = $unicodeCharacters[$i];
        }

        self::$characters = $charactersMap;

        return self::$characters;
    }
}

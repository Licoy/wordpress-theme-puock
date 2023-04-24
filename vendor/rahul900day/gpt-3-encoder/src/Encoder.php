<?php

declare(strict_types=1);

namespace Rahul900day\Gpt3Encoder;

class Encoder
{
    public static function encode(string $text): array
    {
        $bpe_tokens = [];
        preg_match_all("/'s|'t|'re|'ve|'m|'ll|'d| ?\p{L}+| ?\p{N}+| ?[^\s\p{L}\p{N}]+|\s+(?!\S)|\s+/u", $text, $matches);
        $matches = $matches[0];

        foreach ($matches as $token) {
            $token = Character::fromBytes(self::stringToBytes($token));
            $bpe_token = (new Bpe())->tokenize($token);

            $bpe_tokens = [...$bpe_tokens, ...TokenEncoder::encode($bpe_token)];
        }

        return $bpe_tokens;
    }

    public static function decode(array $tokens): string
    {
        $text = TokenEncoder::decode($tokens);

        return self::bytesToString(Character::toBytes($text));
    }

    protected static function stringToBytes(string $string): array
    {
        $encoded = (array) unpack('C*', mb_convert_encoding($string, 'UTF-8'));

        return array_map(fn ($x): string => (string) $x, $encoded);
    }

    protected static function bytesToString(array $bytes): string
    {
        return mb_convert_encoding(pack('C*', ...$bytes), 'UTF-8');
    }
}

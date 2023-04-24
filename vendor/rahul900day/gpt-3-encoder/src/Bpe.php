<?php

declare(strict_types=1);

namespace Rahul900day\Gpt3Encoder;

use Rahul900day\Gpt3Encoder\Support\File;

class Bpe
{
    protected static string $bpe_file;

    protected static array $cache = [];

    protected static array $ranks;

    public function tokenize(string $token): string
    {
        if (isset(self::$cache[$token])) {
            return self::$cache[$token];
        }

        $bpe_token = $this->bpe($token, $this->getBpeRanks());
        self::$cache[$token] = $bpe_token;

        return $bpe_token;
    }

    protected function bpe(string $word, array $bpe_ranks): string
    {
        $characters = mb_str_split($word);
        $pairs = $this->getPairs($characters);

        if ($pairs === []) {
            return $word;
        }

        while (true) {
            $bigram = $this->getMinRankPair($bpe_ranks, $pairs);

            if (! isset($bpe_ranks[implode(',', $bigram)])) {
                break;
            }

            $new_characters = [];
            foreach ($characters as $i => $character) {
                if ($character === $bigram[1] && isset($characters[$i - 1]) && $bigram[0] === $characters[$i - 1]) {
                    continue;
                }

                if ($character === $bigram[0] && isset($characters[$i + 1]) && $characters[$i + 1] === $bigram[1]) {
                    $new_characters[] = $bigram[0].$bigram[1];
                } else {
                    $new_characters[] = $character;
                }
            }

            $characters = $new_characters;

            if (count($characters) === 1) {
                break;
            }

            $pairs = $this->getPairs($characters);
        }

        return implode(' ', $characters);
    }

    protected function getBpeFile(): string
    {
        if (isset(self::$bpe_file)) {
            return self::$bpe_file;
        }

        self::$bpe_file = File::get(__DIR__.'/../data/vocab.bpe');

        return self::$bpe_file;
    }

    protected function getBpeMerges(string $bpeFile): array
    {
        $lines = explode("\n", $bpeFile);
        array_shift($lines); // remove first line

        $bpe_merges = [];
        foreach ($lines as $line) {
            /** @var string[] $merge_pairs */
            $merge_pairs = preg_split('/(\s+)/', $line);
            $merge_pairs = array_filter($merge_pairs, fn ($pair): bool => trim($pair) !== '');

            if (count($merge_pairs) !== 2) {
                continue;
            }

            $bpe_merges[] = $merge_pairs;
        }

        return $bpe_merges;
    }

    protected function getBpeRanks(): array
    {
        if (isset(self::$ranks)) {
            return self::$ranks;
        }

        $bpes = $this->getBpeMerges(self::getBpeFile());
        $ranks = [];
        foreach ($bpes as $rank => $bpe) {
            $ranks[implode(',', $bpe)] = $rank;
        }

        self::$ranks = $ranks;

        return self::$ranks;
    }

    protected function getPairs(array $word): array
    {
        $pairs = [];
        $prev_char = $word[0];
        array_shift($word);

        foreach ($word as $character) {
            $pairs[] = [$prev_char, $character];
            $prev_char = $character;
        }

        return $pairs;
    }

    protected function getMinRankPair(array $bpe_ranks, array $pairs): array
    {
        $min_rank = PHP_INT_MAX;
        $min_rank_pair = null;

        foreach ($pairs as $pair) {
            $rank = $bpe_ranks[implode(',', $pair)] ?? 10e10;

            if ($rank < $min_rank) {
                $min_rank = $rank;
                $min_rank_pair = $pair;
            }
        }

        return $min_rank_pair;
    }
}

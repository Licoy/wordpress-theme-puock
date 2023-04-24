<?php

namespace Gioni06\Gpt3Tokenizer;
class Vocab
{
    private array $vocab;

    public function __construct(string $path = __DIR__ . '/pretrained_vocab_files/vocab.json')
    {
        $this->vocab = json_decode(file_get_contents($path), true);
    }

    public function data(): mixed
    {
        return $this->vocab;
    }
}

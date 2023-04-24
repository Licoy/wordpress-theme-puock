<?php

namespace Gioni06\Gpt3Tokenizer;

class Gpt3TokenizerConfig
{

    private array $config = [
        'mergesPath' => __DIR__ . '/pretrained_vocab_files/merges.txt',
        'vocabPath' => __DIR__ . '/pretrained_vocab_files/vocab.json',
        'useCache' => true,
    ];

    public function mergesPath($path): Gpt3TokenizerConfig
    {
        $this->config['mergesPath'] = $path;
        return $this;
    }

    public function vocabPath($path): Gpt3TokenizerConfig
    {
        $this->config['vocabPath'] = $path;
        return $this;
    }

    public function useCache($useCache): Gpt3TokenizerConfig
    {
        $this->config['useCache'] = $useCache;
        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
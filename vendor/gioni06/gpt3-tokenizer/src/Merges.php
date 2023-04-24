<?php

namespace Gioni06\Gpt3Tokenizer;
class Merges {
    public function __construct(private string $path = __DIR__ . '/pretrained_vocab_files/merges.txt')
    {
    }

    public function bpeMerges(): array
    {
        $lines = [];
        $fp = @fopen($this->path, "r");
        if ($fp) {
            // drop the first line of the buffer
            fgets($fp, 300);
            while (($buffer = fgets($fp, 300)) !== false) {
                $line = array_filter(preg_split("/(\s+)/", $buffer), function($e) {
                    return strlen(trim($e)) > 0;
                });
                $lines[] = $line;
            }
            if (!feof($fp)) {
                throw new Exception("Error: unexpected fgets() fail\n");
            }
            fclose($fp);
        }
        return $lines;
    }
}

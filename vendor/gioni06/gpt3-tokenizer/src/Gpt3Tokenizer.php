<?php

namespace Gioni06\Gpt3Tokenizer;

class Gpt3Tokenizer
{
    const PAT_REGEX = "/'s|'t|'re|'ve|'m|'ll|'d| ?[[:alpha:]]+| ?[[:digit:]]+| ?[^[:space:]\pL\pN]+|\s+(?!\S)|\s+/u";
    private mixed $vocab;
    private array $bpeMerges;
    private array $bpe_ranks;
    private bool $apcuAvailable;

    private array $cache = [];

    private bool $useCache;


    public function __construct(Gpt3TokenizerConfig $config)
    {
        $vocabPath = $config->getConfig()['vocabPath'];
        $vocab = new Vocab($vocabPath);
        $this->vocab = $vocab->data();
        // Free memory that is no longer needed
        unset($vocab);

        $mergesPath = $config->getConfig()['mergesPath'];
        $merges = new Merges($mergesPath);
        $this->bpeMerges = $merges->bpeMerges();
        $this->bpe_ranks = array_combine(Gpt3Tokenizer::zipBpe($this->bpeMerges), range(0, count($this->bpeMerges) - 1));
        // Free memory that is no longer needed
        unset($this->bpeMerges);
        unset($merges);

        $this->apcuAvailable = function_exists('apcu_enabled') && apcu_enabled();
        $this->useCache = $config->getConfig()['useCache'];
    }

    private function cacheSet($key, $val): void
    {
        if ($this->apcuAvailable) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            apcu_store($key, $val);
        } else {
            $this->cache[$key] = $val;
        }
    }

    private function cacheGet($key): mixed
    {
        if ($this->apcuAvailable) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            return apcu_fetch($key);
        } else {
            return $this->cache[$key] ?? null;
        }
    }

    private function cacheExists($key): array|bool
    {
        if ($this->apcuAvailable) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            return apcu_exists($key);
        } else {
            return isset($this->cache[$key]);
        }
    }

    public static function bytes_to_unicode(): array
    {
        // Bytes-to-Unicode is a list of utf-8 byte and a corresponding unicode string.
        // Using this static list is much faster than decoding the utf-8 everytime a character is encountered.
        // Also, it produces the exact output as tokenizer from OpenAI uses. https://beta.openai.com/tokenizer
        return [
            0 => 'Ā',
            1 => 'ā',
            2 => 'Ă',
            3 => 'ă',
            4 => 'Ą',
            5 => 'ą',
            6 => 'Ć',
            7 => 'ć',
            8 => 'Ĉ',
            9 => 'ĉ',
            10 => 'Ċ',
            11 => 'ċ',
            12 => 'Č',
            13 => 'č',
            14 => 'Ď',
            15 => 'ď',
            16 => 'Đ',
            17 => 'đ',
            18 => 'Ē',
            19 => 'ē',
            20 => 'Ĕ',
            21 => 'ĕ',
            22 => 'Ė',
            23 => 'ė',
            24 => 'Ę',
            25 => 'ę',
            26 => 'Ě',
            27 => 'ě',
            28 => 'Ĝ',
            29 => 'ĝ',
            30 => 'Ğ',
            31 => 'ğ',
            32 => 'Ġ',
            33 => '!',
            34 => '"',
            35 => '#',
            36 => '$',
            37 => '%',
            38 => '&',
            39 => '\'',
            40 => '(',
            41 => ')',
            42 => '*',
            43 => '+',
            44 => ',',
            45 => '-',
            46 => '.',
            47 => '/',
            48 => '0',
            49 => '1',
            50 => '2',
            51 => '3',
            52 => '4',
            53 => '5',
            54 => '6',
            55 => '7',
            56 => '8',
            57 => '9',
            58 => ':',
            59 => ';',
            60 => '<',
            61 => '=',
            62 => '>',
            63 => '?',
            64 => '@',
            65 => 'A',
            66 => 'B',
            67 => 'C',
            68 => 'D',
            69 => 'E',
            70 => 'F',
            71 => 'G',
            72 => 'H',
            73 => 'I',
            74 => 'J',
            75 => 'K',
            76 => 'L',
            77 => 'M',
            78 => 'N',
            79 => 'O',
            80 => 'P',
            81 => 'Q',
            82 => 'R',
            83 => 'S',
            84 => 'T',
            85 => 'U',
            86 => 'V',
            87 => 'W',
            88 => 'X',
            89 => 'Y',
            90 => 'Z',
            91 => '[',
            92 => '\\',
            93 => ']',
            94 => '^',
            95 => '_',
            96 => '`',
            97 => 'a',
            98 => 'b',
            99 => 'c',
            100 => 'd',
            101 => 'e',
            102 => 'f',
            103 => 'g',
            104 => 'h',
            105 => 'i',
            106 => 'j',
            107 => 'k',
            108 => 'l',
            109 => 'm',
            110 => 'n',
            111 => 'o',
            112 => 'p',
            113 => 'q',
            114 => 'r',
            115 => 's',
            116 => 't',
            117 => 'u',
            118 => 'v',
            119 => 'w',
            120 => 'x',
            121 => 'y',
            122 => 'z',
            123 => '{',
            124 => '|',
            125 => '}',
            126 => '~',
            127 => 'ġ',
            128 => 'Ģ',
            129 => 'ģ',
            130 => 'Ĥ',
            131 => 'ĥ',
            132 => 'Ħ',
            133 => 'ħ',
            134 => 'Ĩ',
            135 => 'ĩ',
            136 => 'Ī',
            137 => 'ī',
            138 => 'Ĭ',
            139 => 'ĭ',
            140 => 'Į',
            141 => 'į',
            142 => 'İ',
            143 => 'ı',
            144 => 'Ĳ',
            145 => 'ĳ',
            146 => 'Ĵ',
            147 => 'ĵ',
            148 => 'Ķ',
            149 => 'ķ',
            150 => 'ĸ',
            151 => 'Ĺ',
            152 => 'ĺ',
            153 => 'Ļ',
            154 => 'ļ',
            155 => 'Ľ',
            156 => 'ľ',
            157 => 'Ŀ',
            158 => 'ŀ',
            159 => 'Ł',
            160 => 'ł',
            161 => '¡',
            162 => '¢',
            163 => '£',
            164 => '¤',
            165 => '¥',
            166 => '¦',
            167 => '§',
            168 => '¨',
            169 => '©',
            170 => 'ª',
            171 => '«',
            172 => '¬',
            173 => 'Ń',
            174 => '®',
            175 => '¯',
            176 => '°',
            177 => '±',
            178 => '²',
            179 => '³',
            180 => '´',
            181 => 'µ',
            182 => '¶',
            183 => '·',
            184 => '¸',
            185 => '¹',
            186 => 'º',
            187 => '»',
            188 => '¼',
            189 => '½',
            190 => '¾',
            191 => '¿',
            192 => 'À',
            193 => 'Á',
            194 => 'Â',
            195 => 'Ã',
            196 => 'Ä',
            197 => 'Å',
            198 => 'Æ',
            199 => 'Ç',
            200 => 'È',
            201 => 'É',
            202 => 'Ê',
            203 => 'Ë',
            204 => 'Ì',
            205 => 'Í',
            206 => 'Î',
            207 => 'Ï',
            208 => 'Ð',
            209 => 'Ñ',
            210 => 'Ò',
            211 => 'Ó',
            212 => 'Ô',
            213 => 'Õ',
            214 => 'Ö',
            215 => '×',
            216 => 'Ø',
            217 => 'Ù',
            218 => 'Ú',
            219 => 'Û',
            220 => 'Ü',
            221 => 'Ý',
            222 => 'Þ',
            223 => 'ß',
            224 => 'à',
            225 => 'á',
            226 => 'â',
            227 => 'ã',
            228 => 'ä',
            229 => 'å',
            230 => 'æ',
            231 => 'ç',
            232 => 'è',
            233 => 'é',
            234 => 'ê',
            235 => 'ë',
            236 => 'ì',
            237 => 'í',
            238 => 'î',
            239 => 'ï',
            240 => 'ð',
            241 => 'ñ',
            242 => 'ò',
            243 => 'ó',
            244 => 'ô',
            245 => 'õ',
            246 => 'ö',
            247 => '÷',
            248 => 'ø',
            249 => 'ù',
            250 => 'ú',
            251 => 'û',
            252 => 'ü',
            253 => 'ý',
            254 => 'þ',
            255 => 'ÿ',
        ];
    }

    public static function encodeStr(string $str): array {
        $bytes = str_split(bin2hex(mb_convert_encoding($str, 'UTF-8')), 2);
        return array_map(function($byte){
            return hexdec($byte);
        },$bytes);
    }

    public static function decodeStr(array $codes): string {
        $bytes = array_map(function($code) {
            return chr($code);
        }, $codes);
        return implode($bytes);
    }

    public static function get_pairs($input_arr): array
    {
        $pairs = array();
        for ($i = 0; $i < count($input_arr) - 1; $i++) {
            $pairs[] = array($input_arr[$i], $input_arr[$i + 1]);
        }
        // remove duplicates
        return array_unique($pairs, SORT_REGULAR);
    }

    public static function zipBpe(array $bpeMerges): array
    {
        $bpe = [];
        foreach ($bpeMerges as $merge) {
            $bpe[] = $merge[0] . ',' . $merge[1];
        }
        return $bpe;
    }

    public function bpe(string $token): string
    {
        if($this->useCache && $this->cacheExists($token)) {
            return $this->cacheGet($token);
        }

        $chars = mb_str_split($token);
        $pairs = self::get_pairs($chars);
        if(!count($pairs)) {
            return implode(" ", $chars);
        }

        while (true) {
            $minPairs = [];
            foreach ($pairs as $pair) {
                $pairStr = implode(",", $pair);
                if (array_key_exists($pairStr, $this->bpe_ranks)) {
                    $minPairs[$this->bpe_ranks[$pairStr]] = $pair;
                } else {
                    $minPairs[10e10] = $pair;
                }
            }
            ksort($minPairs);

            $bigram = $minPairs[min(array_map(function($x) {
                return intval($x);
            }, array_keys($minPairs)))];

            $bigramStr = implode(",", $bigram);
            if (!array_key_exists($bigramStr, $this->bpe_ranks)) {
                break;
            }

            $first = $bigram[0];
            $second = $bigram[1];
            $new_word = array();
            $i = 0;

            while ($i < count($chars)) {
                $j = array_search($first, array_slice($chars, $i));
                if ($j === false) {
                    $new_word = array_merge($new_word, array_slice($chars, $i));
                    break;
                }
                $new_word = array_merge($new_word, array_slice($chars, $i, $j));
                $i = $i + $j;

                if ($chars[$i] === $first && $i < count($chars) - 1 && $chars[$i + 1] === $second) {
                    $new_word[] = $first . $second;
                    $i = $i + 2;
                } else {
                    $new_word[] = $chars[$i];
                    $i++;
                }
            }
            $chars = $new_word;
            if (count($chars) === 1) {
                break;
            } else {
                $pairs = self::get_pairs($chars);
            }
        }
        $result = implode(" ", $chars);
        if($this->useCache) {
            $this->cacheSet($token, $result);
        }
        return $result;
    }

    public function encode(string $text): array
    {
        $byte_encoder = self::bytes_to_unicode();
        $bpe_tokens = array();
        $matches = array();
        preg_match_all(self::PAT_REGEX, $text, $matches);
        foreach ($matches[0] as $token) {
            $token = implode(array_map(function($x) use ($byte_encoder) {
                return $byte_encoder[$x];
            }, self::encodeStr($token)));

            $new_tokens = array_map(function($x) {
                return $this->vocab[$x];
            }, explode(' ', $this->bpe($token)));
            $bpe_tokens = array_merge($bpe_tokens, $new_tokens);
        }
        return $bpe_tokens;
    }

    /**
     * Encodes a given text into chunks of Byte-Pair Encoded (BPE) tokens, with each chunk containing a specified
     * maximum number of tokens.
     * @param string $text The input text to be encoded.
     * @param int $maxTokenPerChunk The maximum number of tokens allowed per chunk.
     * @return int[][] An array of arrays containing BPE token chunks.
     */
    public function encodeInChunks(string $text, int $maxTokenPerChunk): array
    {
        $byte_encoder = self::bytes_to_unicode();

        $bpe_tokens_chunks = array();
        $bpe_tokens_current_chunk = array();

        $matches = array();
        preg_match_all(self::PAT_REGEX, $text, $matches);
        foreach ($matches[0] as $token) {
            $token = implode(array_map(function($x) use ($byte_encoder) {
                return $byte_encoder[$x];
            }, self::encodeStr($token)));

            $new_tokens = array_map(function($x) {
                return $this->vocab[$x];
            }, explode(' ', $this->bpe($token)));

            if ((count($bpe_tokens_current_chunk) + count($new_tokens)) > $maxTokenPerChunk) {
                $bpe_tokens_chunks[] = $bpe_tokens_current_chunk;
                $bpe_tokens_current_chunk = array();
            }

            $bpe_tokens_current_chunk = array_merge($bpe_tokens_current_chunk, $new_tokens);
        }

        if (count($bpe_tokens_current_chunk) > 0) {
            $bpe_tokens_chunks[] = $bpe_tokens_current_chunk;
        }

        return $bpe_tokens_chunks;
    }

    /**
     * Takes a given text and chunks it into encoded segments, with each segment containing a specified maximum
     * number of tokens.
     * @param string $text The input text to be encoded.
     * @param int $maxTokenPerChunk The maximum number of tokens allowed per chunk.
     * @return string[] An array of strings containing the encoded text.
     */
    public function chunk(string $text, int $maxTokenPerChunk): array
    {
        return array_map(
            [$this, 'decode'],
            $this->encodeInChunks($text, $maxTokenPerChunk)
        );
    }

    public function decode(array $tokens): string
    {
        $decoder = array_flip($this->vocab);
        $byte_decoder = array_flip(self::bytes_to_unicode());

        $text = array_map(function($x) use ($decoder) {
            return $decoder[$x];
        }, $tokens);

        $text = implode($text);
        $chars = mb_str_split($text);
        $decodedChars = array();
        for ($i = 0; $i < count($chars); $i++) {
            $decodedChars[] = $byte_decoder[$chars[$i]];
        }
        return self::decodeStr($decodedChars);
    }

    public function count(string $text): int
    {
        $tokens = self::encode($text);
        return count($tokens);
    }
}

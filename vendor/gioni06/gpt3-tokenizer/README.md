# GPT3Tokenizer for PHP

This is a PHP port of the GPT-3 tokenizer. It is based on the [original Python implementation](https://huggingface.co/docs/transformers/model_doc/gpt2#transformers.GPT2Tokenizer) and the [Nodejs implementation](https://github.com/latitudegames/GPT-3-Encoder).

GPT-2 and GPT-3 use a technique called byte pair encoding to convert text into a sequence of integers, which are then used as input for the model.
When you interact with the OpenAI API, you may find it useful to calculate the amount of tokens in a given text before sending it to the API.

If you want to learn more, read the [Summary of the tokenizers](https://huggingface.co/docs/transformers/tokenizer_summary) from Hugging Face.

## Support ⭐️

If you find my work useful, I would be thrilled if you could show your support by giving this project a star ⭐️. 
It only takes a second and it would mean a lot to me. Your star will not only make me feel warm and fuzzy inside, but it will also help reach more people who can benefit from this project.


## Installation
Install the package from [Packagist](https://packagist.org/packages/gioni06/gpt3-tokenizer) using Composer:

```bash
composer require gioni06/gpt3-tokenizer
```

## Testing
Loading the vocabulary files consumes a lot of memory. You might need to increase the phpunit memory limit.
https://stackoverflow.com/questions/46448294/phpunit-coverage-allowed-memory-size-of-536870912-bytes-exhausted
```bash
./vendor/bin/phpunit -d memory_limit=-1 tests/
```

## Use the configuration Class

```php
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;

// default vocab path
// default merges path
// caching enabled
$defaultConfig = new Gpt3TokenizerConfig();

$customConfig = new Gpt3TokenizerConfig();
$customConfig
    ->vocabPath('custom_vocab.json') // path to a custom vocabulary file
    ->mergesPath('custom_merges.txt') // path to a custom merges file
    ->useCache(false)
```

### A note on caching
The tokenizer will try to use `apcu` for caching, if that is not available it will use a plain PHP `array`.
You will see slightly better performance for long texts when using the cache. The cache is enabled by default.

## Encode a text

```php
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;
use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;

$config = new Gpt3TokenizerConfig();
$tokenizer = new Gpt3Tokenizer($config);
$text = "This is some text";
$tokens = $tokenizer->encode($text);
// [1212,318,617,2420]
```

## Decode a text

```php
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;
use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;

$config = new Gpt3TokenizerConfig();
$tokenizer = new Gpt3Tokenizer($config);
$tokens = [1212,318,617,2420]
$text = $tokenizer->decode($tokens);
// "This is some text"
```

## Count the number of tokens in a text

```php
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;
use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;

$config = new Gpt3TokenizerConfig();
$tokenizer = new Gpt3Tokenizer($config);
$text = "This is some text";
$numberOfTokens = $tokenizer->count($text);
// 4
```

## Encode a given text into chunks of tokens, with each chunk containing a specified maximum number of tokens.

This method is useful when handling large texts that need to be divided into smaller chunks for further processing.


```php
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;
use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;

$config = new Gpt3TokenizerConfig();
$tokenizer = new Gpt3Tokenizer($config);
$text = "1 2 hello，world 3 4";
$tokenizer->encodeInChunks($text, 5)
// [[16, 362, 23748], [171, 120, 234, 6894, 513], [604]]
```

## Takes a given text and chunks it into encoded segments, with each segment containing a specified maximum number of tokens.

This method leverages the encodeInChunks method for encoding the text into Byte-Pair Encoded (BPE) tokens and then decodes these tokens back into text.

```php
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;
use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;

$config = new Gpt3TokenizerConfig();
$tokenizer = new Gpt3Tokenizer($config);
$text = "1 2 hello，world 3 4";
$tokenizer->chunk($text, 5)
// ['1 2 hello', '，world 3', ' 4']
```

## License
This project uses the Apache License 2.0 license. See the [LICENSE](LICENSE) file for more information.

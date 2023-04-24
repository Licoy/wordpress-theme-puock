<?php /** @noinspection SpellCheckingInspection */

namespace Gioni06\Gpt3Tokenizer\Tests;

use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;
use PHPUnit\Framework\TestCase;

class Gpt3TokenizerTest extends TestCase {

    private Gpt3Tokenizer $tokenizer;

    protected function setUp(): void
    {
        parent::setUp();
        $config = new Gpt3TokenizerConfig();
        $this->tokenizer = new Gpt3Tokenizer($config);
    }
    public function test_encodeStr_function(): void
    {
        $this->assertEquals([ '32', '119', '111', '114', '108', '100' ], Gpt3Tokenizer::encodeStr(" world"));
        $this->assertEquals([ '32', '240', '159', '140', '141' ], Gpt3Tokenizer::encodeStr(" 🌍"));
        $this->assertEquals([ '240', '159', '148', '173' ], Gpt3Tokenizer::encodeStr("🔭"));
    }


    public function test_decodeStr_function(): void
    {
        $this->assertEquals(" world", Gpt3Tokenizer::decodeStr([ '32', '119', '111', '114', '108', '100' ]));
        $this->assertEquals(" 🌍", Gpt3Tokenizer::decodeStr([ '32', '240', '159', '140', '141' ]));
        $this->assertEquals("🔭", Gpt3Tokenizer::decodeStr([ '240', '159', '148', '173' ]));
    }

    public function test_get_pairs_function()
    {
        $this->assertEquals(array(
            [ 'Ġ', 'w' ],
            [ 'w', 'o' ],
            [ 'o', 'r' ],
            [ 'r', 'l' ],
            [ 'l', 'd' ]
        ), Gpt3Tokenizer::get_pairs([ 'Ġ', 'w', 'o', 'r', 'l', 'd' ]));

        $this->assertEquals(array(
            [ 'ĠðŁĳ', 'ĭ' ]
        ), Gpt3Tokenizer::get_pairs([ 'ĠðŁĳ', 'ĭ' ]));

        $this->assertEquals(array(
            [ 'he', 'l' ], [ 'l', 'l' ], [ 'l', 'o' ]
        ), Gpt3Tokenizer::get_pairs([ 'he', 'l', 'l', 'o' ]));
    }

    public function test_bpe_function()
    {
        $this->assertEquals("Ġhas Own Property", $this->tokenizer->bpe("ĠhasOwnProperty"));
    }

    public function test_bytes_to_unicode_function()
    {
        $this->assertEquals("Ā", Gpt3Tokenizer::bytes_to_unicode()[0]);
        $this->assertEquals("d", Gpt3Tokenizer::bytes_to_unicode()[100]);
        $this->assertEquals("È", Gpt3Tokenizer::bytes_to_unicode()[200]);
        $this->assertEquals("ÿ", Gpt3Tokenizer::bytes_to_unicode()[255]);
        $this->assertCount(256, Gpt3Tokenizer::bytes_to_unicode());
    }

    /*
     * Test public interface of the GPT-3 Tokenizer
     */

    public function test_encode_function()
    {
        $longText = <<<EOT
BPE ensures that the most common words are represented in the vocabulary as a single token while the rare words are broken down into two or more subword tokens and this is in agreement with what a subword-based tokenization algorithm does.
EOT;

        $this->assertEquals(array(1212,318,617,2420), $this->tokenizer->encode("This is some text"));
        $this->assertEquals([10134, 23858, 21746], $this->tokenizer->encode("hasOwnProperty"));
        $this->assertEquals([10163, 2231, 30924, 3829], $this->tokenizer->encode("1234567890"));
        $this->assertEquals([ 15496, 11854, 616, 1468, 1545 ], $this->tokenizer->encode("Hello darkness my old friend"));
        $this->assertEquals([33, 3732, 641, 354, 10203, 403, 1010, 794, 2150, 82, 585, 77, 2150], $this->tokenizer->encode("Binnenschiffsuntersuchungsordnung"));
        $this->assertEquals([33, 11401, 19047, 326, 262, 749, 2219, 2456, 389, 7997, 287, 262, 25818, 355, 257, 2060, 11241, 981, 262, 4071, 2456, 389, 5445, 866, 656, 734, 393, 517, 850, 4775, 16326, 290, 428, 318, 287, 4381, 351, 644, 257, 850, 4775, 12, 3106, 11241, 1634, 11862, 857, 13], $this->tokenizer->encode($longText));
    }

    public function test_decode_function()
    {
        $tokens = [33, 11401, 19047, 326, 262, 749, 2219, 2456, 389, 7997, 287, 262, 25818, 355, 257, 2060, 11241, 981, 262, 4071, 2456, 389, 5445, 866, 656, 734, 393, 517, 850, 4775, 16326, 290, 428, 318, 287, 4381, 351, 644, 257, 850, 4775, 12, 3106, 11241, 1634, 11862, 857, 13];
        $longText = <<<EOT
BPE ensures that the most common words are represented in the vocabulary as a single token while the rare words are broken down into two or more subword tokens and this is in agreement with what a subword-based tokenization algorithm does.
EOT;
        $this->assertEquals($longText, $this->tokenizer->decode($tokens));
        $this->assertEquals('Binnenschiffsuntersuchungsordnung', $this->tokenizer->decode([33, 3732, 641, 354, 10203, 403, 1010, 794, 2150, 82, 585, 77, 2150]));
    }

    public function test_count_function()
    {
        $this->assertEquals(6, $this->tokenizer->count("Hello darkness my old friend!"));
    }

    public function test_new_function()
    {
        // You can create a new tokenizer with a different vocabulary
        $config = new Gpt3TokenizerConfig();
        $config
            ->vocabPath(__DIR__ . '/__fixtures__/vocab_example.json')
            ->mergesPath(__DIR__ . '/__fixtures__/merges_example.txt');
        $testTokenizer = new Gpt3Tokenizer($config);

        $this->assertInstanceOf(Gpt3Tokenizer::class, $this->tokenizer);
        $this->assertInstanceOf(Gpt3Tokenizer::class, $testTokenizer);
    }

    public function test_long_text()
    {
        $config = new Gpt3TokenizerConfig();
        $config->useCache(true);
        $cachedTokenizer = new Gpt3Tokenizer($config);

        $newConfig = new Gpt3TokenizerConfig();
        $newConfig->useCache(false);
        $uncachedTokenizer = new Gpt3Tokenizer($newConfig);

        $longText = file_get_contents(__DIR__ . '/__fixtures__/long_text.txt');

        $cachedStart = microtime(true);
        $cachedTokenizer->encode($longText);
        $cachedEnd = microtime(true);

        $uncachedStart = microtime(true);
        $uncachedTokenizer->encode($longText);
        $uncachedEnd = microtime(true);
        $this->assertLessThan($uncachedEnd - $uncachedStart, $cachedEnd - $cachedStart);
    }

    public function test_config()
    {
        $config = new Gpt3TokenizerConfig();
        $config
            ->mergesPath(__DIR__ . '/__fixtures__/merges_example.txt')
            ->useCache(false);
        $this->assertStringEndsWith('merges_example.txt', $config->getConfig()['mergesPath']);
        $this->assertStringEndsWith('vocab.json', $config->getConfig()['vocabPath']);
        $this->assertFalse($config->getConfig()['useCache']);
    }

    public function test_regression_issue_5()
    {
        $config = new Gpt3TokenizerConfig();
        $tokenizer = new Gpt3Tokenizer($config);
        $tokens = $tokenizer->encode("🔭");
        $this->assertEquals([8582, 242, 255], $tokens);
    }

    public function test_open_ai_example()
    {
        $config = new Gpt3TokenizerConfig();
        $tokenizer = new Gpt3Tokenizer($config);
        $openAiExample = <<<EOT
Many words map to one token, but some don't: indivisible.

Unicode characters like emojis may be split into many tokens containing the underlying bytes: 🤚🏾

Sequences of characters commonly found next to each other may be grouped together: 1234567890
EOT;

        $tokens = $tokenizer->encode($openAiExample);
        $this->assertEquals([7085, 2456, 3975, 284, 530, 11241, 11, 475, 617, 836, 470, 25, 773, 452, 12843, 13, 198, 198, 3118, 291, 1098, 3435, 588, 795, 13210, 271, 743, 307, 6626, 656, 867, 16326, 7268, 262, 10238, 9881, 25, 12520, 97, 248, 8582, 237, 122, 198, 198, 44015, 3007, 286, 3435, 8811, 1043, 1306, 284, 1123, 584, 743, 307, 32824, 1978, 25, 17031, 2231, 30924, 3829], $tokens);
    }

    public function test_chunk()
    {
        $config = new Gpt3TokenizerConfig();
        $tokenizer = new Gpt3Tokenizer($config);
        $text = "1 2 hello，world 3 4";

        // "，" maps to 3 tokens, we want to make sure they end up in the same chunk
        $this->assertEquals(
            [
                [16, 362, 23748],
                [171, 120, 234, 6894, 513],
                [604],
            ],
            $tokenizer->encodeInChunks($text, 5)
        );
        $this->assertEquals(
            [
                '1 2 hello',
                '，world 3',
                ' 4',
            ],
            $tokenizer->chunk($text, 5)
        );
    }
}

<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\Completions;

use Tectalic\OpenAi\Models\AbstractModel;

final class CreateRequest extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['model'];

    /**
     * ID of the model to use. You can use the List models API to see all of your
     * available models, or see our Model overview for descriptions of them.
     *
     * @var string
     */
    public $model;

    /**
     * The prompt(s) to generate completions for, encoded as a string, array of
     * strings, array of tokens, or array of token arrays.
     * Note that <|endoftext|> is the document separator that the model sees during
     * training, so if a prompt is not specified the model will generate as if from the
     * beginning of a new document.
     *
     * Default Value: '<|endoftext|>'
     *
     * @var mixed
     */
    public $prompt;

    /**
     * The suffix that comes after a completion of inserted text.
     *
     * Default Value: null
     *
     * Example: 'test.'
     *
     * @var string|null
     */
    public $suffix;

    /**
     * The maximum number of tokens to generate in the completion.
     * The token count of your prompt plus max_tokens cannot exceed the model's context
     * length. Most models have a context length of 2048 tokens (except for the newest
     * models, which support 4096).
     *
     * Default Value: 16
     *
     * Example: 16
     *
     * @var int|null
     */
    public $max_tokens;

    /**
     * What sampling temperature to use, between 0 and 2. Higher values like 0.8 will
     * make the output more random, while lower values like 0.2 will make it more
     * focused and deterministic.
     * We generally recommend altering this or top_p but not both.
     *
     * Default Value: 1
     *
     * Example: 1
     *
     * @var float|int|null
     */
    public $temperature;

    /**
     * An alternative to sampling with temperature, called nucleus sampling, where the
     * model considers the results of the tokens with top_p probability mass. So 0.1
     * means only the tokens comprising the top 10% probability mass are considered.
     * We generally recommend altering this or temperature but not both.
     *
     * Default Value: 1
     *
     * Example: 1
     *
     * @var float|int|null
     */
    public $top_p;

    /**
     * How many completions to generate for each prompt.
     * Note: Because this parameter generates many completions, it can quickly consume
     * your token quota. Use carefully and ensure that you have reasonable settings for
     * max_tokens and stop.
     *
     * Default Value: 1
     *
     * Example: 1
     *
     * @var int|null
     */
    public $n;

    /**
     * Whether to stream back partial progress. If set, tokens will be sent as
     * data-only server-sent events as they become available, with the stream
     * terminated by a data: [DONE] message.
     *
     * Default Value: false
     *
     * @var bool|null
     */
    public $stream;

    /**
     * Include the log probabilities on the logprobs most likely tokens, as well the
     * chosen tokens. For example, if logprobs is 5, the API will return a list of the
     * 5 most likely tokens. The API will always return the logprob of the sampled
     * token, so there may be up to logprobs+1 elements in the response.
     * The maximum value for logprobs is 5. If you need more than this, please contact
     * us through our Help center and describe your use case.
     *
     * Default Value: null
     *
     * @var int|null
     */
    public $logprobs;

    /**
     * Echo back the prompt in addition to the completion
     *
     * Default Value: false
     *
     * @var bool|null
     */
    public $echo;

    /**
     * Up to 4 sequences where the API will stop generating further tokens. The
     * returned text will not contain the stop sequence.
     *
     * Default Value: null
     *
     * @var mixed
     */
    public $stop;

    /**
     * Number between -2.0 and 2.0. Positive values penalize new tokens based on
     * whether they appear in the text so far, increasing the model's likelihood to
     * talk about new topics.
     * See more information about frequency and presence penalties.
     *
     * Default Value: 0
     *
     * @var float|int|null
     */
    public $presence_penalty;

    /**
     * Number between -2.0 and 2.0. Positive values penalize new tokens based on their
     * existing frequency in the text so far, decreasing the model's likelihood to
     * repeat the same line verbatim.
     * See more information about frequency and presence penalties.
     *
     * Default Value: 0
     *
     * @var float|int|null
     */
    public $frequency_penalty;

    /**
     * Generates best_of completions server-side and returns the "best" (the one with
     * the highest log probability per token). Results cannot be streamed.
     * When used with n, best_of controls the number of candidate completions and n
     * specifies how many to return – best_of must be greater than n.
     * Note: Because this parameter generates many completions, it can quickly consume
     * your token quota. Use carefully and ensure that you have reasonable settings for
     * max_tokens and stop.
     *
     * Default Value: 1
     *
     * @var int|null
     */
    public $best_of;

    /**
     * Modify the likelihood of specified tokens appearing in the completion.
     * Accepts a json object that maps tokens (specified by their token ID in the GPT
     * tokenizer) to an associated bias value from -100 to 100. You can use this
     * tokenizer tool (which works for both GPT-2 and GPT-3) to convert text to token
     * IDs. Mathematically, the bias is added to the logits generated by the model
     * prior to sampling. The exact effect will vary per model, but values between -1
     * and 1 should decrease or increase likelihood of selection; values like -100 or
     * 100 should result in a ban or exclusive selection of the relevant token.
     * As an example, you can pass {"50256": -100} to prevent the <|endoftext|> token
     * from being generated.
     *
     * Default Value: null
     *
     * @var \Tectalic\OpenAi\Models\Completions\CreateRequestLogitBias|null
     */
    public $logit_bias;

    /**
     * A unique identifier representing your end-user, which can help OpenAI to monitor
     * and detect abuse. Learn more.
     *
     * Example: 'user-1234'
     *
     * @var string
     */
    public $user;
}

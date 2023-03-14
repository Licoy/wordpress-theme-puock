<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\Embeddings;

use Tectalic\OpenAi\Models\AbstractModel;

final class CreateRequest extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['model', 'input'];

    /** @var bool */
    protected $ignoreMissing = false;

    /**
     * ID of the model to use. You can use the List models API to see all of your
     * available models, or see our Model overview for descriptions of them.
     *
     * @var string
     */
    public $model;

    /**
     * Input text to get embeddings for, encoded as a string or array of tokens. To get
     * embeddings for multiple inputs in a single request, pass an array of strings or
     * array of token arrays. Each input must not exceed 8192 tokens in length.
     *
     * Example: 'The quick brown fox jumped over the lazy dog'
     *
     * @var mixed
     */
    public $input;

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

<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\Edits;

use Tectalic\OpenAi\Models\AbstractModel;

final class CreateRequest extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['model', 'instruction'];

    /**
     * ID of the model to use. You can use the text-davinci-edit-001 or
     * code-davinci-edit-001 model with this endpoint.
     *
     * @var string
     */
    public $model;

    /**
     * The input text to use as a starting point for the edit.
     *
     * Default Value: ''
     *
     * Example: 'What day of the wek is it?'
     *
     * @var string|null
     */
    public $input;

    /**
     * The instruction that tells the model how to edit the prompt.
     *
     * Example: 'Fix the spelling mistakes.'
     *
     * @var string
     */
    public $instruction;

    /**
     * How many edits to generate for the input and instruction.
     *
     * Default Value: 1
     *
     * Example: 1
     *
     * @var int|null
     */
    public $n;

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
}

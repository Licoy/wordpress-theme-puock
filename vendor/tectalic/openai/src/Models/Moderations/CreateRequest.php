<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\Moderations;

use Tectalic\OpenAi\Models\AbstractModel;

final class CreateRequest extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['input'];

    /**
     * The input text to classify
     *
     * @var mixed
     */
    public $input;

    /**
     * Two content moderations models are available: text-moderation-stable and
     * text-moderation-latest.
     * The default is text-moderation-latest which will be automatically upgraded over
     * time. This ensures you are always using our most accurate model. If you use
     * text-moderation-stable, we will provide advanced notice before updating the
     * model. Accuracy of text-moderation-stable may be slightly lower than for
     * text-moderation-latest.
     *
     * Default Value: 'text-moderation-latest'
     *
     * Example: 'text-moderation-stable'
     *
     * @var string
     */
    public $model;
}

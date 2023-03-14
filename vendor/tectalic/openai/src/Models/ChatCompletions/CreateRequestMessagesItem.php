<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\ChatCompletions;

use Tectalic\OpenAi\Models\AbstractModel;

final class CreateRequestMessagesItem extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['role', 'content'];

    /**
     * The role of the author of this message.
     *
     * Allowed values: 'system', 'user', 'assistant'
     *
     * @var string
     */
    public $role;

    /**
     * The contents of the message
     *
     * @var string
     */
    public $content;

    /**
     * The name of the user in a multi-user chat
     *
     * @var string
     */
    public $name;
}

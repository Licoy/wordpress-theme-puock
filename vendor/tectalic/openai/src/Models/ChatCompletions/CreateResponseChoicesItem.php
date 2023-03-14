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

final class CreateResponseChoicesItem extends AbstractModel
{
    /** @var int */
    public $index;

    /** @var \Tectalic\OpenAi\Models\ChatCompletions\CreateResponseChoicesItemMessage */
    public $message;

    /** @var string */
    public $finish_reason;
}

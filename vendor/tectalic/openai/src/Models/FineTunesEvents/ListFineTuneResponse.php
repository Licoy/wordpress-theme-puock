<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\FineTunesEvents;

use Tectalic\OpenAi\Models\AbstractModel;

final class ListFineTuneResponse extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['object', 'data'];

    /** @var string */
    public $object;

    /** @var \Tectalic\OpenAi\Models\FineTunesEvents\ListFineTuneResponseDataItem[] */
    public $data;
}

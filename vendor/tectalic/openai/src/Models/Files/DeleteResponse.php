<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\Files;

use Tectalic\OpenAi\Models\AbstractModel;

final class DeleteResponse extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['id', 'object', 'deleted'];

    /** @var string */
    public $id;

    /** @var string */
    public $object;

    /** @var bool */
    public $deleted;
}

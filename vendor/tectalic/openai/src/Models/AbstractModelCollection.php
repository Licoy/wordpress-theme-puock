<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models;

use JsonSerializable;
use Spatie\DataTransferObject\DataTransferObjectCollection;

abstract class AbstractModelCollection extends DataTransferObjectCollection implements JsonSerializable
{
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $collection = $this->collection;
        foreach ($collection as &$value) {
            if ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            }
        }
        return $collection;
    }
}

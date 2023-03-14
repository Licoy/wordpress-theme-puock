<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tests;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;

final class NullAuth implements Authentication
{
    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request;
    }
}

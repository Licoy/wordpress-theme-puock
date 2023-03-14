<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tests\Unit;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Tectalic\OpenAi\Authentication;

final class AuthenticationTest extends TestCase
{
    public function testAuthenticate(): void
    {
        $request = (new Authentication('access-token'))->authenticate(
            (new Psr17Factory())->createRequest('GET', '/')
        );
        $this->assertSame(
            'Bearer access-token',
            $request->getHeaderLine('Authorization')
        );
    }
}

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

final class MockUri
{
    /** @var string */
    public $base;

    public function __construct()
    {
        $this->base = getenv('OPENAI_CLIENT_TEST_BASE_URI') ?: 'http://127.0.0.1:4010';
    }
}

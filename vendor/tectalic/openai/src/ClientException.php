<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * HTTP Client related Exception.
 */
class ClientException extends Exception implements ClientExceptionInterface
{
}

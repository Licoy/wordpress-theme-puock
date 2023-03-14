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

use Exception;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Psr\Http\Message\RequestInterface;

trait AssertValidateTrait
{
    public function assertValidate(RequestInterface $request): void
    {
        $this->expectNotToPerformAssertions();

        $path = dirname(__DIR__) . '/tests/openapi.yaml';
        $specification = \file_get_contents($path);
        if ($specification === false) {
            throw new Exception('Missing required test data.');
        }
        $validator = (new ValidatorBuilder())
            ->fromYaml($specification)
            ->getRequestValidator();

        $validator->validate($request);
    }
}

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

use Http\Message\Authentication as Authentication1;
use Http\Message\Authentication\Bearer;
use Psr\Http\Message\RequestInterface;

final class Authentication implements Authentication1
{
    /** @var Bearer */
    private $auth;

    /**
     * Authenticate a request with HTTP Bearer authentication.
     *
     * @param string $token Token
     */
    public function __construct(string $token)
    {
        $this->auth = new \Http\Message\Authentication\Bearer($token);
    }

    /**
     * Alter the request to add the authentication credentials.
     */
    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $this->auth->authenticate($request);
    }
}
